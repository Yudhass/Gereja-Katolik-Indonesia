// preload.js - Jembatan aman antara renderer dan main process.
const { contextBridge, ipcRenderer } = require('electron');

contextBridge.exposeInMainWorld('api', {
  getDefaults: () => ipcRenderer.invoke('app:get-defaults'),
  testDb: (cfg) => ipcRenderer.invoke('db:test', cfg),
  fetchData: (cfg) => ipcRenderer.invoke('db:fetch', cfg),
  openUrls: (urls) => ipcRenderer.invoke('app:open-urls', urls),
});
