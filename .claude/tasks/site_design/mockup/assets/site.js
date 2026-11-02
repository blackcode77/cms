// Мобильное меню и фильтр документов. Без зависимостей, работает с file://
(function(){
  var t=document.querySelector('.nav-toggle'),n=document.getElementById('nav');
  if(t&&n){t.addEventListener('click',function(){var o=n.classList.toggle('is-open');t.setAttribute('aria-expanded',o)})}
  var f=document.querySelector('.filters');
  if(f){f.addEventListener('click',function(e){
    var b=e.target.closest('button');if(!b)return;
    f.querySelectorAll('button').forEach(function(x){x.setAttribute('aria-pressed',x===b)});
    var c=b.dataset.cat;
    document.querySelectorAll('[data-doc]').forEach(function(li){li.hidden=(c!=='all'&&li.dataset.doc!==c)});
    document.querySelectorAll('[data-doc-group]').forEach(function(g){g.hidden=(c!=='all'&&g.dataset.docGroup!==c)});
  })}
  var tabs=document.querySelectorAll('[data-vessel]');
  tabs.forEach(function(b){b.addEventListener('click',function(){
    tabs.forEach(function(x){x.setAttribute('aria-pressed',x===b)});
    document.querySelectorAll('[data-vessel-panel]').forEach(function(p){p.hidden=p.dataset.vesselPanel!==b.dataset.vessel});
  })});
})();
