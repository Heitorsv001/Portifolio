import { readFile } from 'node:fs/promises';
import { pathToFileURL } from 'node:url';

export async function verifyWorker(config, { token, accountId, origin, request = fetch }) {
  if (!token || !/^[a-f0-9]{32}$/i.test(accountId || '')) {
    throw new Error('Configure CLOUDFLARE_API_TOKEN e CLOUDFLARE_ACCOUNT_ID nos secrets do GitHub.');
  }
  if (config.name !== 'heitorvareiro' || config.workers_dev !== true
      || config.assets?.directory !== './dist'
      || config.assets.html_handling !== 'force-trailing-slash'
      || config.assets.not_found_handling !== '404-page'
      || config.main || config.routes || config.route || config.env
      || origin !== 'https://heitorvareiro.heitorsvareiro.workers.dev') {
    throw new Error('Configuração divergente do Worker e endereço de produção aprovados.');
  }

  async function get(path) {
    const response = await request(`https://api.cloudflare.com/client/v4/accounts/${accountId}/workers/${path}`, {
      headers: { Authorization: `Bearer ${token}` },
      signal: AbortSignal.timeout(30000),
    });
    if (!response.ok) {
      throw new Error(`Verificação do Worker recusada (HTTP ${response.status}). Nenhum deploy autorizado.`);
    }
    const data = await response.json();
    if (data.success !== true || !data.result) {
      throw new Error('Resposta inválida da Cloudflare. Nenhum deploy autorizado.');
    }
    return data.result;
  }

  const worker = await get(`scripts/${config.name}/subdomain`);
  const account = await get('subdomain');
  if (worker.enabled !== true || `https://${config.name}.${account.subdomain}.workers.dev` !== origin) {
    throw new Error('Worker existente ou subdomínio divergente do endereço de produção.');
  }
  console.log(`Worker existente verificado: ${config.name}; destino: ${origin}`);
}

if (process.argv[1] && import.meta.url === pathToFileURL(process.argv[1]).href) {
  try {
    const config = JSON.parse(await readFile(new URL('../wrangler.jsonc', import.meta.url), 'utf8'));
    await verifyWorker(config, {
      token: process.env.CLOUDFLARE_API_TOKEN,
      accountId: process.env.CLOUDFLARE_ACCOUNT_ID,
      origin: process.env.APP_ORIGIN,
    });
  } catch {
    console.error('Verificação interrompida. Confira os secrets, as permissões do token, o Worker heitorvareiro e seu subdomínio no painel. Nenhum deploy autorizado.');
    process.exitCode = 1;
  }
}
