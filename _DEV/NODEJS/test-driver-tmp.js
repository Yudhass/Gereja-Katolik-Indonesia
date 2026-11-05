const { Builder } = require("selenium-webdriver");
const chrome = require("selenium-webdriver/chrome");
(async () => {
  const o = new chrome.Options();
  o.addArguments("--headless=new", "--no-sandbox", "--disable-dev-shm-usage");
  const d = await new Builder().forBrowser("chrome").setChromeOptions(o).build();
  const ver = await d.getCapabilities().then((c) => c.getBrowserVersion());
  console.log("DRIVER_OK " + ver);
  await d.quit();
})().catch((e) => {
  console.error("DRIVER_FAIL " + String(e.message).split("\n")[0]);
  process.exit(1);
});
