import assert from 'node:assert/strict';
import { spawnSync } from 'node:child_process';
import { copyFile, mkdir, mkdtemp, readFile, rm, writeFile } from 'node:fs/promises';
import { tmpdir } from 'node:os';
import { join } from 'node:path';
import test from 'node:test';
import { verifyWorker } from '../scripts/verify-worker.mjs';

const config = JSON.parse(await readFile(new URL('../wrangler.jsonc', import.meta.url), 'utf8'));
const options = {
  token: 'token-ficticio-de-teste',
  accountId: 'a'.repeat(32),
  origin: 'https://heitorvareiro.heitorsvareiro.workers.dev',
};
const response = (result) => Response.json({ success: true, result });
const publicPage = () => new Response('<!doctype html><html lang="pt-BR"><title>Portfólio</title></html>', {
  headers: { 'Content-Type': 'text/html; charset=utf-8' },
});
const workerUrl = `https://api.cloudflare.com/client/v4/accounts/${options.accountId}/workers/scripts/heitorvareiro/subdomain`;
const success = (url) => {
  if (url === workerUrl) return response({ enabled: true, previews_enabled: false });
  assert.equal(url, `${options.origin}/`, 'Somente a API do Worker e a URL pública podem ser consultadas');
  return publicPage();
};
const noRequest = async () => assert.fail('A API não deve ser consultada');

function rejects(configChange, optionsChange, message) {
  return assert.rejects(verifyWorker({ ...config, ...configChange }, {
    ...options, request: noRequest, ...optionsChange,
  }), message);
}

test('aceita token restrito ao Worker sem consultar subdomínio da conta nem enviar secrets à URL pública', async () => {
  const paths = [];
  await verifyWorker(config, { ...options, request: async (url, init) => {
    paths.push(url);
    assert.equal(init.method, 'GET');
    assert.equal(init.redirect, 'error');
    assert.ok(init.signal instanceof AbortSignal);
    if (url === `${options.origin}/`) {
      assert.deepEqual(init.headers, { Accept: 'text/html' });
      assert.ok(!JSON.stringify(init).includes(options.token));
      assert.ok(!JSON.stringify(init).includes(options.accountId));
    } else {
      assert.equal(init.headers.Authorization, `Bearer ${options.token}`);
      assert.equal(init.headers.Accept, 'application/json');
    }
    if (url.endsWith('/workers/subdomain')) return new Response(null, { status: 403 });
    return success(url);
  } });
  assert.deepEqual(paths, [
    workerUrl,
    `${options.origin}/`,
  ]);
});

for (const [field, secret] of [['token', 'CLOUDFLARE_API_TOKEN'], ['accountId', 'CLOUDFLARE_ACCOUNT_ID']]) {
  for (const value of [undefined, '', ' \n']) {
    test(`identifica secret ausente ${secret}: ${JSON.stringify(value)}`, async () => {
      await rejects({}, { [field]: value }, new RegExp(`Secret ausente: ${secret}`));
    });
  }
}

test('distingue Account ID inválido e divergente sem imprimir seus valores', async () => {
  for (const accountId of ['id-invalido', 'a'.repeat(31), ` ${options.accountId}`]) {
    await rejects({}, { accountId }, /Account ID inválido/);
  }
  await rejects({ account_id: 'b'.repeat(32) }, {}, /Account ID divergente/);
  await verifyWorker({ ...config, account_id: options.accountId }, { ...options, request: success });
});

test('recusa token malformado sem consultar a API', async () => {
  for (const token of ['Bearer token', 'token\n', ' token']) {
    await rejects({}, { token }, /CLOUDFLARE_API_TOKEN malformado/);
  }
});

test('distingue nome do Worker incorreto de origem divergente', async () => {
  await rejects({ name: 'heitorsvareiro' }, {}, /Nome do Worker incorreto/);
  for (const origin of [undefined, 'https://outro.example', `${options.origin}/`]) {
    await rejects({}, { origin }, /APP_ORIGIN ausente ou divergente/);
  }
});

test('preserva bloqueios de configuração, rotas, ambientes e diretório de publicação', async () => {
  for (const change of [
    { workers_dev: false }, { assets: undefined }, { assets: { ...config.assets, directory: './public' } },
    { assets: { ...config.assets, html_handling: 'auto-trailing-slash' } },
    { assets: { ...config.assets, not_found_handling: 'none' } },
    { main: 'index.php' }, { routes: [] }, { route: 'example.com/*' }, { env: {} },
  ]) {
    await rejects(change, {}, /Configuração divergente/);
  }
  for (const value of [null, [], 'config']) {
    await assert.rejects(verifyWorker(value, { ...options, request: noRequest }), /Configuração inválida/);
  }
});

for (const [status, message] of [
  [401, /Falha de autenticação.*HTTP 401/],
  [403, /Permissão insuficiente.*HTTP 403/],
  [404, /Worker heitorvareiro não encontrado ou não visível.*HTTP 404/],
  [400, /Falha da API.*HTTP 400/], [429, /Falha da API.*HTTP 429/], [500, /Falha da API.*HTTP 500/],
]) {
  test(`bloqueia HTTP ${status} na API do Worker antes de consultar a URL pública`, async () => {
    await assert.rejects(verifyWorker(config, { ...options, request: async (url) => {
      assert.equal(url, workerUrl);
      return new Response('<html>erro</html>', { status });
    } }), message);
  });
}

test('informa somente códigos numéricos seguros, sem repetir mensagens ou campos da API', async () => {
  await assert.rejects(verifyWorker(config, { ...options, request: async () => Response.json({
    success: false,
    errors: [{ code: 10000, message: options.token }, { code: options.token }],
    result: { credential: options.accountId },
  }, { status: 403 }) }), (error) => {
    assert.match(error.message, /Códigos da API: 10000/);
    assert.ok(!error.message.includes(options.token));
    assert.ok(!error.message.includes(options.accountId));
    return true;
  });
});

test('recusa success=false mesmo com HTTP 200 e preserva o código da API', async () => {
  await assert.rejects(verifyWorker(config, { ...options, request: async () => Response.json({
    success: false, result: {}, errors: [{ code: 10000 }],
  }) }), /HTTP 200.*10000.*success=false/);
});

test('distingue falha de rede e timeout sem divulgar a exceção original', async () => {
  for (const error of [new Error(options.token), new DOMException(options.accountId, 'TimeoutError')]) {
    await assert.rejects(verifyWorker(config, { ...options, request: async () => { throw error; } }), (caught) => {
      assert.match(caught.message, /Falha de rede ou tempo limite/);
      assert.ok(!caught.message.includes(options.token));
      assert.ok(!caught.message.includes(options.accountId));
      return true;
    });
  }
});

test('recusa respostas HTTP, JSON, success e result inválidos', async () => {
  await assert.rejects(verifyWorker(config, { ...options, request: async () => undefined }), /Resposta HTTP inválida/);
  await assert.rejects(verifyWorker(config, { ...options, request: async () => new Response('não é JSON') }), /JSON ilegível/);
  for (const body of [null, [], {}, { success: 'true' }, { success: true },
    { success: true, result: null }, { success: true, result: [] }, { success: true, result: 'string' }]) {
    await assert.rejects(verifyWorker(config, { ...options, request: async () => Response.json(body) }), /Resposta inválida/);
  }
});

test('distingue enabled inválido de workers.dev desabilitado; previews não autorizam produção', async () => {
  for (const enabled of [undefined, null, 'true', 1, false]) {
    await assert.rejects(verifyWorker(config, { ...options, request: async (url) => {
      assert.equal(url, workerUrl);
      return response({ enabled, previews_enabled: true });
    } }), enabled === false ? /workers.dev desabilitado/ : /result.enabled deve ser booleano/);
  }
});

for (const status of [201, 204, 301, 302, 401, 403, 404, 500]) {
  test(`bloqueia URL pública com HTTP ${status} mesmo após autenticação válida no Worker`, async () => {
    await assert.rejects(verifyWorker(config, { ...options, request: async (url) => {
      return url === workerUrl ? success(url) : new Response(null, { status });
    } }), new RegExp(`URL pública de produção indisponível \\(HTTP ${status}\\)`));
  });
}

test('bloqueia falha de rede ou redirecionamento na URL pública sem expor a exceção', async () => {
  await assert.rejects(verifyWorker(config, { ...options, request: async (url) => {
    if (url === workerUrl) return success(url);
    throw new Error(options.token);
  } }), (error) => {
    assert.match(error.message, /Falha de rede, redirecionamento ou tempo limite.*URL pública/);
    assert.ok(!error.message.includes(options.token));
    return true;
  });
});

test('bloqueia resposta pública inválida, conteúdo não HTML, HTML vazio e leitura interrompida', async () => {
  for (const [makeResponse, message] of [
    [() => undefined, /Resposta HTTP inválida/],
    [() => Response.json({ success: true }), /Content-Type text\/html/],
    [() => new Response('texto'), /Content-Type text\/html/],
    [() => new Response('  ', { headers: { 'Content-Type': 'text/html' } }), /HTML vazio/],
    [() => ({ status: 200, headers: new Headers({ 'Content-Type': 'text/html' }),
      text: async () => { throw new Error(options.token); } }), /Falha de rede ou tempo limite ao ler/],
  ]) {
    await assert.rejects(verifyWorker(config, { ...options, request: async (url) => {
      return url === workerUrl ? success(url) : makeResponse();
    } }), message);
  }
});

async function runCli({ env = {}, source = JSON.stringify(config), preload } = {}) {
  const directory = await mkdtemp(join(tmpdir(), 'portfolio-verify-worker-'));
  try {
    await mkdir(join(directory, 'scripts'));
    await copyFile(new URL('../scripts/verify-worker.mjs', import.meta.url), join(directory, 'scripts/verify-worker.mjs'));
    if (source !== null) await writeFile(join(directory, 'wrangler.jsonc'), source);
    const mock = preload || `globalThis.fetch = async () => { throw new Error('rede bloqueada no teste'); };`;
    const result = spawnSync(process.execPath, ['--import', `data:text/javascript,${encodeURIComponent(mock)}`, join(directory, 'scripts/verify-worker.mjs')], {
      env: { CLOUDFLARE_API_TOKEN: options.token, CLOUDFLARE_ACCOUNT_ID: options.accountId, APP_ORIGIN: options.origin, ...env },
      encoding: 'utf8', timeout: 10000,
    });
    assert.ifError(result.error);
    return result;
  } finally {
    await rm(directory, { recursive: true, force: true });
  }
}

test('CLI retorna exit 1 e identifica secret ausente sem acessar a rede', async () => {
  const result = await runCli({ env: { CLOUDFLARE_API_TOKEN: '' } });
  assert.equal(result.status, 1);
  assert.match(result.stderr, /Secret ausente: CLOUDFLARE_API_TOKEN.*Nenhum deploy autorizado/);
});

test('CLI trata arquivo ausente e JSON inválido sem imprimir conteúdo sensível', async () => {
  for (const [source, message] of [[null, /ler wrangler.jsonc/], [options.token, /interpretar wrangler.jsonc/]]) {
    const result = await runCli({ source });
    assert.equal(result.status, 1);
    assert.match(result.stderr, message);
    assert.ok(!result.stderr.includes(options.token));
  }
});

test('CLI preserva diagnóstico HTTP sem divulgar corpo, token ou Account ID', async () => {
  const result = await runCli({ preload: `globalThis.fetch = async () => Response.json({
    success: false, errors: [{ code: 10000, message: process.env.CLOUDFLARE_API_TOKEN }],
    account: process.env.CLOUDFLARE_ACCOUNT_ID
  }, { status: 403 });` });
  assert.equal(result.status, 1);
  assert.match(result.stderr, /Permissão insuficiente.*HTTP 403.*10000.*Nenhum deploy autorizado/);
  for (const value of [options.token, options.accountId]) {
    assert.ok(!(result.stdout + result.stderr).includes(value));
  }
});

test('CLI retorna exit 1 quando a URL pública falha, mesmo com autenticação válida no Worker', async () => {
  for (const status of [403, 404, 500]) {
    const result = await runCli({ preload: `globalThis.fetch = async (url) => {
      if (url === process.env.APP_ORIGIN + '/') return new Response(null, { status: ${status} });
      return Response.json({ success: true, result: { enabled: true } });
    };` });
    assert.equal(result.status, 1);
    assert.match(result.stderr, new RegExp(`URL pública de produção indisponível \\(HTTP ${status}\\)`));
    assert.match(result.stderr, /Nenhum deploy autorizado/);
    assert.equal(result.stdout, '');
  }
});

test('CLI só aprova após API autenticada e URL pública válidas e informa a limitação sobre o vínculo da conta', async () => {
  const result = await runCli({ preload: `let calls = 0;
    globalThis.fetch = async (url, init) => {
      calls++;
      if (url === process.env.APP_ORIGIN + '/') {
        if ('Authorization' in init.headers) throw new Error('credencial na URL pública');
        return new Response('<html>Portfólio</html>', { headers: { 'Content-Type': 'text/html' } });
      }
      if (init.headers.Authorization !== 'Bearer ' + process.env.CLOUDFLARE_API_TOKEN ||
          url !== 'https://api.cloudflare.com/client/v4/accounts/' + process.env.CLOUDFLARE_ACCOUNT_ID +
            '/workers/scripts/heitorvareiro/subdomain') throw new Error('ambiente divergente');
      return Response.json({ success: true, result: { enabled: true, previews_enabled: false } });
    };
    process.on('exit', () => { if (calls !== 2) process.exitCode = 2; });` });
  assert.equal(result.status, 0, result.stderr);
  assert.match(result.stdout, /Worker existente verificado: heitorvareiro/);
  assert.match(result.stderr, /Limitação: o subdomínio da conta não foi consultado/);
  assert.match(result.stderr, /não comprova o vínculo entre ambos/);
  for (const value of [options.token, options.accountId]) {
    assert.ok(!(result.stdout + result.stderr).includes(value));
  }
});
