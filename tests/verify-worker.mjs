import assert from 'node:assert/strict';
import { readFile } from 'node:fs/promises';
import test from 'node:test';
import { verifyWorker } from '../scripts/verify-worker.mjs';

const config = JSON.parse(await readFile(new URL('../wrangler.jsonc', import.meta.url), 'utf8'));
const options = {
  token: 'token-ficticio-de-teste',
  accountId: 'a'.repeat(32),
  origin: 'https://heitorvareiro.heitorsvareiro.workers.dev',
};
const response = (result) => ({ ok: true, json: async () => ({ success: true, result }) });

test('confirma Worker existente e endereço na conta, usando somente GET', async () => {
  const paths = [];
  await verifyWorker(config, { ...options, request: async (url, init) => {
    paths.push(url);
    assert.equal(init.method, undefined);
    assert.equal(init.headers.Authorization, `Bearer ${options.token}`);
    return response(url.endsWith('/scripts/heitorvareiro/subdomain') ? { enabled: true } : { subdomain: 'heitorsvareiro' });
  } });
  assert.deepEqual(paths, [
    `https://api.cloudflare.com/client/v4/accounts/${options.accountId}/workers/scripts/heitorvareiro/subdomain`,
    `https://api.cloudflare.com/client/v4/accounts/${options.accountId}/workers/subdomain`,
  ]);
});

test('recusa credenciais ausentes e configuração divergente antes de consultar a API', async () => {
  const request = async () => assert.fail('A API não deve ser consultada');
  for (const change of [{ token: '' }, { accountId: '' }, { origin: 'https://outro.example' }]) {
    await assert.rejects(verifyWorker(config, { ...options, ...change, request }));
  }
  for (const change of [{ name: 'worker-inexistente' }, { assets: { directory: './public' } }, { main: 'index.php' }, { env: {} }]) {
    await assert.rejects(verifyWorker({ ...config, ...change }, { ...options, request }));
  }
});

test('recusa Worker inexistente, token inválido e indisponibilidade da API', async () => {
  for (const status of [404, 401, 403, 500]) {
    await assert.rejects(verifyWorker(config, { ...options, request: async () => ({ ok: false, status }) }));
  }
  await assert.rejects(verifyWorker(config, { ...options, request: async () => { throw new Error('Falha de rede'); } }));
});

test('recusa resposta inválida, workers.dev desabilitado e conta com outro subdomínio', async () => {
  await assert.rejects(verifyWorker(config, { ...options, request: async () => ({ ok: true, json: async () => ({ success: false }) }) }));
  for (const [enabled, subdomain] of [[false, 'heitorsvareiro'], [true, 'outra-conta']]) {
    await assert.rejects(verifyWorker(config, { ...options, request: async (url) => response(
      url.endsWith('/scripts/heitorvareiro/subdomain') ? { enabled } : { subdomain },
    ) }));
  }
});
