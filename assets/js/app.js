'use strict';
(function(){
 const toggle=document.getElementById('menuToggle');
 const mobile=document.getElementById('mobileMenu');
 if(toggle&&mobile){
  toggle.addEventListener('click',()=>{const open=mobile.hidden;mobile.hidden=!open;toggle.setAttribute('aria-expanded',String(open));});
  document.addEventListener('keydown',e=>{if(e.key==='Escape'&&!mobile.hidden){mobile.hidden=true;toggle.setAttribute('aria-expanded','false');toggle.focus();}});
  document.addEventListener('click',e=>{if(!mobile.hidden&&!mobile.contains(e.target)&&!toggle.contains(e.target)){mobile.hidden=true;toggle.setAttribute('aria-expanded','false');}});
 }
 document.querySelectorAll('form[data-search]').forEach(form=>{
  const start=form.elements.namedItem('start_date');const end=form.elements.namedItem('end_date');
  const earliest=new Date();const today=[earliest.getFullYear(),String(earliest.getMonth()+1).padStart(2,'0'),String(earliest.getDate()).padStart(2,'0')].join('-');
  if(start){start.min=today;}if(end){end.min=today;}
  const error=form.querySelector('[data-search-error]');
  const check=()=>{if(start&&end&&start.value&&end.value){end.min=start.value; if(end.value<start.value){if(error){error.textContent='Return date must be on or after pickup.';error.classList.add('error');}return false;}} if(error){error.classList.remove('error');error.textContent='Final availability and rental terms require staff confirmation.';}return true;};
  [start,end].forEach(x=>x&&x.addEventListener('change',check));
  form.addEventListener('submit',e=>{if(!check())e.preventDefault();else{
    const d1=form.elements.namedItem('start_date'),t1=form.elements.namedItem('start_time');
    const d2=form.elements.namedItem('end_date'),t2=form.elements.namedItem('end_time');
    if(d1&&d2&&t1&&t2&&d1.value&&d2.value){
      const from=new Date(d1.value+'T'+t1.value+':00'),to=new Date(d2.value+'T'+t2.value+':00');
      if(to<=from){e.preventDefault();if(error){error.classList.add('error');error.textContent='Return must be later than pickup.';}}
    }
  }});
 });
 const main=document.getElementById('galleryMain');
 if(main)document.querySelectorAll('[data-gallery-src]').forEach(btn=>btn.addEventListener('click',()=>{main.src=btn.getAttribute('data-gallery-src');main.alt=btn.getAttribute('data-gallery-alt')||'Vehicle photograph';}));
 if('serviceWorker' in navigator&&window.isSecureContext){window.addEventListener('load',()=>navigator.serviceWorker.register('/sw.js').catch(()=>{}));}
})();
