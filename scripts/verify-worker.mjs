import { readFile } from 'node:fs/promises';
import { pathToFileURL } from 'node:url';

class VerificationError extends Error {}

function fail(message) {
  throw new VerificationError(message);
}

function isObject(value) {
  return value !== null && typeof value === 'object' && !Array.isArray(value);
}

function apiCodes(data) {
  const codes = Array.isArray(data?.errors)
    ? data.errors.map((error) => error?.code)
      .filter((code) => Number.isInteger(code) && code >= 1000 && code <= 999999).slice(0, 5)
    : [];
  return codes.length ? ` Códigos da API: ${codes.join(', ')}.` : '';
}

export async function verifyWorker(config, { token, accountId, origin, request = fetch }) {
  for (const [name, value] of [['CLOUDFLARE_API_TOKEN', token], ['CLOUDFLARE_ACCOUNT_ID', accountId]]) {
    if (typeof value !== 'string' || !value.trim()) {
      fail(`Secret ausente: ${name}. Configure-o nos secrets de Actions do repositório.`);
    }
  }
  if (!/^[a-f0-9]{32}$/i.test(accountId)) {
    fail('Account ID inválido: CLOUDFLARE_ACCOUNT_ID deve conter 32 caracteres hexadecimais, sem espaços. Use o ID da conta, não o Zone ID.');
  }
  if (/\s/.test(token)) {
    fail('Secret CLOUDFLARE_API_TOKEN malformado: informe apenas o token, sem espaços, quebras de linha ou prefixo Bearer.');
  }
  if (!isObject(config)) {
    fail('Configuração inválida: wrangler.jsonc deve conter um objeto.');
  }
  if (config.name !== 'heitorvareiro') {
    fail('Nome do Worker incorreto em wrangler.jsonc: o nome aprovado é heitorvareiro, não o subdomínio da conta.');
  }
  if (config.account_id !== undefined && config.account_id !== accountId) {
    fail('Account ID divergente: account_id em wrangler.jsonc difere de CLOUDFLARE_ACCOUNT_ID. A verificação e o Wrangler devem usar a mesma conta.');
  }
  if (origin !== 'https://heitorvareiro.heitorsvareiro.workers.dev') {
    fail('APP_ORIGIN ausente ou divergente: use https://heitorvareiro.heitorsvareiro.workers.dev, sem barra final.');
  }
  if (config.workers_dev !== true
      || config.assets?.directory !== './dist'
      || config.assets.html_handling !== 'force-trailing-slash'
      || config.assets.not_found_handling !== '404-page'
      || config.main || config.routes || config.route || config.env) {
    fail('Configuração divergente em wrangler.jsonc: mantenha workers_dev=true, assets em ./dist, force-trailing-slash e 404-page, sem main, routes, route ou env.');
  }

  async function get(path, resource) {
    let response;
    let data;
    try {
      response = await request(`https://api.cloudflare.com/client/v4/accounts/${accountId}/workers/${path}`, {
        method: 'GET',
        headers: { Authorization: `Bearer ${token}`, Accept: 'application/json' },
        redirect: 'error',
        signal: AbortSignal.timeout(30000),
      });
    } catch {
      fail(`Falha de rede ou tempo limite ao consultar ${resource}. Confira conectividade, DNS e disponibilidade da API Cloudflare.`);
    }
    if (!response || !Number.isInteger(response.status) || typeof response.ok !== 'boolean') {
      fail(`Resposta HTTP inválida ao consultar ${resource}.`);
    }
    try {
      data = await response.json();
    } catch {
      if (response.ok) {
        fail(`Resposta inválida ao consultar ${resource}: JSON ilegível ou leitura interrompida por falha de rede/tempo limite.`);
      }
    }
    const context = `${resource} (HTTP ${response.status}).${apiCodes(data)}`;
    if (!response.ok) {
      if (response.status === 401) {
        fail(`Falha de autenticação ao consultar ${context} Confira se o token está válido, ativo e não expirou.`);
      }
      if (response.status === 403) {
        fail(`Permissão insuficiente ao consultar ${context} Confira o escopo do token e a conta indicada por CLOUDFLARE_ACCOUNT_ID; uma conta incorreta também pode causar 403. Não conceda permissões administrativas para diagnosticar.`);
      }
      if (response.status === 404) {
        fail(`Worker heitorvareiro não encontrado ou não visível na conta informada (HTTP 404).${apiCodes(data)} Confira CLOUDFLARE_ACCOUNT_ID, o nome no painel e o escopo do token. Nenhum Worker será criado por esta verificação.`);
      }
      fail(`Falha da API ao consultar ${context} Confira a disponibilidade da Cloudflare; em HTTP 400, revise também o Account ID e o escopo do token.`);
    }
    if (!isObject(data) || typeof data.success !== 'boolean') {
      fail(`Resposta inválida ao consultar ${context} Campo success ausente ou inválido.`);
    }
    if (!data.success) {
      fail(`A API recusou a consulta de ${context} success=false; confira token, escopo e Account ID. HTTP de sucesso não autoriza deploy quando a API informa falha.`);
    }
    if (!isObject(data.result)) {
      fail(`Resposta inválida ao consultar ${context} Campo result ausente ou inválido.`);
    }
    return data.result;
  }

  const worker = await get(`scripts/${config.name}/subdomain`, 'Worker heitorvareiro');
  if (typeof worker.enabled !== 'boolean') {
    fail('Resposta inválida do Worker: result.enabled deve ser booleano.');
  }
  if (!worker.enabled) {
    fail('workers.dev desabilitado para heitorvareiro: result.enabled=false. Confira Domains & Routes no painel; previews_enabled não habilita a URL de produção.');
  }

  let publicResponse;
  let html;
  try {
    publicResponse = await request(`${origin}/`, {
      method: 'GET',
      headers: { Accept: 'text/html' },
      redirect: 'error',
      signal: AbortSignal.timeout(30000),
    });
  } catch {
    fail('Falha de rede, redirecionamento ou tempo limite ao consultar a URL pública de produção.');
  }
  if (!publicResponse || !Number.isInteger(publicResponse.status)) {
    fail('Resposta HTTP inválida da URL pública de produção.');
  }
  if (publicResponse.status !== 200) {
    fail(`URL pública de produção indisponível (HTTP ${publicResponse.status}). A disponibilidade pública não substitui a autenticação da API do Worker.`);
  }
  if (publicResponse.headers?.get('content-type')?.split(';')[0].trim().toLowerCase() !== 'text/html') {
    fail('Resposta inválida da URL pública de produção: esperado Content-Type text/html.');
  }
  try {
    html = await publicResponse.text();
  } catch {
    fail('Falha de rede ou tempo limite ao ler a página pública de produção.');
  }
  if (!html.trim()) {
    fail('Resposta inválida da URL pública de produção: HTML vazio.');
  }
  console.warn('Limitação: o subdomínio da conta não foi consultado. A API confirmou o Worker no Account ID informado e a URL pública respondeu, mas isso não comprova o vínculo entre ambos. Confira esse vínculo no painel Cloudflare.');
  console.log(`Worker existente verificado: ${config.name}; URL pública disponível: ${origin}`);
}

if (process.argv[1] && import.meta.url === pathToFileURL(process.argv[1]).href) {
  try {
    let source;
    try {
      source = await readFile(new URL('../wrangler.jsonc', import.meta.url), 'utf8');
    } catch {
      fail('Não foi possível ler wrangler.jsonc. Confira a existência e as permissões do arquivo.');
    }
    let config;
    try {
      config = JSON.parse(source);
    } catch {
      fail('Não foi possível interpretar wrangler.jsonc. Esta verificação exige JSON estrito, sem comentários nem vírgulas finais, como na configuração versionada.');
    }
    await verifyWorker(config, {
      token: process.env.CLOUDFLARE_API_TOKEN,
      accountId: process.env.CLOUDFLARE_ACCOUNT_ID,
      origin: process.env.APP_ORIGIN,
    });
  } catch (error) {
    const message = error instanceof VerificationError ? error.message : 'Falha interna inesperada durante a verificação.';
    console.error(`Verificação interrompida: ${message} Nenhum deploy autorizado.`);
    process.exitCode = 1;
  }
}
