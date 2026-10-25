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
const success = (url) => response(url.endsWith('/scripts/heitorvareiro/subdomain')
  ? { enabled: true, previews_enabled: false } : { subdomain: 'heitorsvareiro' });
const noRequest = async () => assert.fail('A API não deve ser consultada');

function rejects(configChange, optionsChange, message) {
  return assert.rejects(verifyWorker({ ...config, ...configChange }, {
    ...options, request: noRequest, ...optionsChange,
  }), message);
}

test('confirma conta e Worker existente usando somente GET e o mesmo Account ID', async () => {
  const paths = [];
  await verifyWorker(config, { ...options, request: async (url, init) => {
    paths.push(url);
    assert.equal(init.method, 'GET');
    assert.equal(init.headers.Authorization, `Bearer ${options.token}`);
    assert.equal(init.headers.Accept, 'application/json');
    assert.equal(init.redirect, 'error');
    assert.ok(init.signal instanceof AbortSignal);
    return success(url);
  } });
  assert.deepEqual(paths, [
    `https://api.cloudflare.com/client/v4/accounts/${options.accountId}/workers/subdomain`,
    `https://api.cloudflare.com/client/v4/accounts/${options.accountId}/workers/scripts/heitorvareiro/subdomain`,
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

for (const resource of ['conta', 'Worker']) {
  for (const [status, message] of [
    [401, /Falha de autenticação.*HTTP 401/],
    [403, /Permissão insuficiente.*HTTP 403/],
    [404, resource === 'conta' ? /Conta ou subdomínio não encontrado.*HTTP 404/ : /Worker heitorvareiro não encontrado.*HTTP 404/],
    [400, /Falha da API.*HTTP 400/], [429, /Falha da API.*HTTP 429/], [500, /Falha da API.*HTTP 500/],
  ]) {
    test(`distingue HTTP ${status} ao consultar ${resource}, mesmo com corpo não JSON`, async () => {
      await assert.rejects(verifyWorker(config, { ...options, request: async (url) => {
        if (resource === 'Worker' && url.endsWith('/workers/subdomain')) return success(url);
        return new Response('<html>erro</html>', { status });
      } }), message);
    });
  }
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

test('recusa subdomínio inválido ou inesperado antes de consultar o Worker', async () => {
  for (const subdomain of [undefined, null, '', true, 'outra-conta']) {
    let calls = 0;
    await assert.rejects(verifyWorker(config, { ...options, request: async () => {
      calls++;
      return response({ subdomain });
    } }), subdomain === 'outra-conta' ? /Subdomínio inesperado.*CLOUDFLARE_ACCOUNT_ID/ : /Resposta inválida.*result.subdomain/);
    assert.equal(calls, 1);
  }
});

test('distingue enabled inválido de workers.dev desabilitado; previews não autorizam produção', async () => {
  for (const enabled of [undefined, null, 'true', 1, false]) {
    await assert.rejects(verifyWorker(config, { ...options, request: async (url) => {
      return url.endsWith('/workers/subdomain') ? success(url) : response({ enabled, previews_enabled: true });
    } }), enabled === false ? /workers.dev desabilitado/ : /result.enabled deve ser booleano/);
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

test('CLI recebe secrets e APP_ORIGIN do ambiente e retorna exit 0 somente após duas consultas válidas', async () => {
  const result = await runCli({ preload: `let calls = 0;
    globalThis.fetch = async (url, init) => {
      if (init.headers.Authorization !== 'Bearer ' + process.env.CLOUDFLARE_API_TOKEN ||
          !url.includes('/accounts/' + process.env.CLOUDFLARE_ACCOUNT_ID + '/')) throw new Error('ambiente divergente');
      calls++;
      return Response.json({ success: true, result: url.endsWith('/workers/subdomain')
        ? { subdomain: 'heitorsvareiro' } : { enabled: true, previews_enabled: false } });
    };
    process.on('exit', () => { if (calls !== 2) process.exitCode = 2; });` });
  assert.equal(result.status, 0, result.stderr);
  assert.match(result.stdout, /Worker existente verificado: heitorvareiro/);
  assert.equal(result.stderr, '');
});
