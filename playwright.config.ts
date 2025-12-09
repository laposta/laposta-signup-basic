import { config as loadEnv } from 'dotenv';
import { defineConfig, devices } from '@playwright/test';

loadEnv({ path: '.env.local', override: true });
loadEnv();

const baseURL = process.env.WP_BASE_URL || 'http://localhost:8889';

export default defineConfig({
  testDir: './tests/e2e',
  /* Run a single worker by default to avoid stepping on the same WP data. */
  workers: 1,
  timeout: 60_000,
  expect: {
    timeout: 10_000,
  },
  fullyParallel: false,
  forbidOnly: !!process.env.CI,
  retries: process.env.CI ? 1 : 0,
  reporter: [['list'], ['html', { outputFolder: 'playwright-report' }]],
  globalSetup: './tests/e2e/global-setup.ts',
  use: {
    baseURL,
    trace: 'on-first-retry',
    storageState: '.playwright/.auth/admin.json',
    ignoreHTTPSErrors: true,
  },
  projects: [
    {
      name: 'chromium',
      use: { ...devices['Desktop Chrome'] },
    },
  ],
});
