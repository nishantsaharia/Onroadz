const CACHE='saafaar-shell-v7';
const SHELL=['/offline/','/assets/css/aether.css?v=7','/assets/js/app.js','/favicon.svg'];
self.addEventListener('install',event=>{event.waitUntil(caches.open(CACHE).then(c=>c.addAll(SHELL)).then(()=>self.skipWaiting()));});
self.addEventListener('activate',event=>{event.waitUntil(caches.keys().then(keys=>Promise.all(keys.filter(k=>k!==CACHE).map(k=>caches.delete(k)))).then(()=>self.clients.claim()));});
self.addEventListener('fetch',event=>{
 const req=event.request;
 if(req.mode!=='navigate'||req.method!=='GET')return;
 const u=new URL(req.url);
 if(u.origin!==self.location.origin||u.search||/^\/(admin|api|booking-confirmation|manage-booking|request-booking|enquire|search)(\/|$)/.test(u.pathname))return;
 event.respondWith(fetch(req,{cache:'no-store'}).catch(()=>caches.match('/offline/')));
});
