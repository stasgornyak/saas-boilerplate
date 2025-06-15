require('dotenv').config();

const { defineConfig } = require("cypress");

module.exports = defineConfig({
  video: false,
  defaultCommandTimeout: 12000,
  viewportWidth: 1366,
  viewportHeight: 768,
  fixturesFolder: "tests/e2e/fixtures",
  screenshotsFolder: "tests/e2e/screenshots",
  videosFolder: "tests/e2e/videos",
  e2e: {
    supportFile: "tests/e2e/support/index.js",
    specPattern: "tests/e2e/specs/**/*.{js,jsx,ts,tsx}",
    setupNodeEvents(on, config) {
      const PROTOCOL_BACKSLASHES = "//";

      const baseUrl = process.env.APP_URL;
      const [protocol] = baseUrl.split(PROTOCOL_BACKSLASHES);
      const isFullBaseUrl = protocol === "https:" || protocol === "http:";

      config.env.baseUrl = baseUrl && isFullBaseUrl ? baseUrl : "";

      return config;
    },
    experimentalRunAllSpecs: true,
    experimentalMemoryManagement: true,
    responseTimeout: 60000,
  },
});
