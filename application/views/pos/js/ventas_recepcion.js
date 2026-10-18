(() => {
'use strict';
const base = (window.POS_BASE_URL || '/').replace(/\/?$/, '/');
const endpoint = (name) => base + 'POS/' + name;
const $ = (id) => document.getElementById(id);
const money = (v) => Number(v || 0).toLocaleString('es-PE', {minimumFractionDigits:2, maximumFractionDigits:2});
const esc = (v) => String(v ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
async function request(name, method='GET', data=null) {
  const options = {method, headers:{'Accept':'application/json'}};
  if (data !== null) { options.headers['Content-Type']='application/json'; options.body=JSON.stringify(data); }
  const response = await fetch(endpoint(name), options);
  let result;
  try { result = await response.json(); } catch (_) { throw new Error('El servidor no devolvió JSON. Revisa la ruta y los errores PHP.'); }
  if (!response.ok || result.status !== 'OK') throw new Error(result.msg || 'No se pudo completar la operación.');
  return result.data;
}
function message(text, ok=false) {
  const el=$('pos-message'); if(!el) { alert(text); return; }
  el.textContent=text; el.className='pos-msg show '+(ok?'ok':'error');
  el.scrollIntoView({behavior:'smooth',block:'nearest'});
}
function hideMessage(){const el=$('pos-message'); if(el) el.className='pos-msg';}
function initCart({saveEndpoint, includePayment=false}) {
  let products=[], cart=[];
  const productSelect=$('pos-product'), quantityInput=$('pos-quantity'), cartBody=$('pos-cart'), totalNode=$('pos-total');
  const renderProducts=()=>{
    productSelect.innerHTML='<option value="">Selecciona producto...</option>'+products.map(p=>{
      const price=Number(p.sale_price ?? p.price_sell ?? p.product_price ?? 0);
      return `<option value="${Number(p.id_product)}">${esc(p.product_name)} — S/ ${money(price)} (stock: ${Number(p.product_stock||0)})</option>`;
    }).join('');
  };
  const renderCart=()=>{
    if(!cart.length) cartBody.innerHTML='<tr><td colspan="5">Sin productos.</td></tr>';
    else cartBody.innerHTML=cart.map((it,i)=>`<tr><td>${esc(it.product_name)}</td><td>S/ ${money(it.price)}</td><td>${it.quantity}</td><td>S/ ${money(it.price*it.quantity)}</td><td><button type="button" class="pos-btn danger" data-remove="${i}">Quitar</button></td></tr>`).join('');
    totalNode.textContent=money(cart.reduce((sum,it)=>sum+it.price*it.quantity,0));
  };
  async function loadProducts(){products=await request('get_productos');renderProducts();}
  $('pos-add').addEventListener('click',()=>{
    hideMessage();
    const id=Number(productSelect.value), qty=Number(quantityInput.value);
    const p=products.find(x=>Number(x.id_product)===id);
    if(!p) return message('Selecciona un producto.');
    if(!Number.isInteger(qty)||qty<1) return message('Ingresa una cantidad válida.');
    const existing=cart.find(x=>x.id_product===id);
    const next=(existing?existing.quantity:0)+qty;
    if(next>Number(p.product_stock||0)) return message('La cantidad supera el stock disponible.');
    if(existing) existing.quantity=next;
    else cart.push({id_product:id,product_name:p.product_name,price:Number(p.sale_price??p.price_sell??p.product_price??0),quantity:qty});
    renderCart(); quantityInput.value='1';
  });
  cartBody.addEventListener('click',e=>{
    const btn=e.target.closest('[data-remove]'); if(!btn)return;
    cart.splice(Number(btn.dataset.remove),1);renderCart();
  });
  $('pos-save').addEventListener('click',async()=>{
    hideMessage();
    if(!cart.length)return message('Agrega al menos un producto.');
    const payload={items:cart.map(i=>({id_product:i.id_product,quantity:i.quantity})),notes:$('pos-notes')?.value||''};
    if(includePayment){
      payload.customer_name=$('pos-customer')?.value||'';
      payload.payment_method=$('pos-payment')?.value||'';
      if(!payload.payment_method)return message('Selecciona el método de pago.');
    } else {
      payload.id_reservation=Number($('pos-room')?.value||0);
      if(!payload.id_reservation)return message('Selecciona una habitación ocupada.');
    }
    try{
      const result=await request(saveEndpoint,'POST',payload);
      cart=[];renderCart();
      message(`Operación registrada. Número: ${result.id||'—'} · Total: S/ ${money(result.total)}`,true);
      if($('pos-customer'))$('pos-customer').value='';
      if($('pos-notes'))$('pos-notes').value='';
    }catch(e){message(e.message);}
  });
  loadProducts().catch(e=>message(e.message));
}
async function loadRooms(){
  const select=$('pos-room'); if(!select)return;
  try{
    const rooms=await request('get_habitaciones');
    select.innerHTML='<option value="">Selecciona habitación...</option>'+rooms.map(r=>
      `<option value="${Number(r.id_reservation)}">Hab. ${esc(r.room_number)} — ${esc(r.customer_name||'Huésped')} (reserva ${Number(r.id_reservation)})</option>`
    ).join('');
    if(!rooms.length)select.innerHTML='<option value="">No hay habitaciones ocupadas</option>';
  }catch(e){message(e.message);select.innerHTML='<option value="">Error al cargar</option>';}
}
window.POS={base,endpoint,request,$,money,esc,message,hideMessage,initCart,loadRooms};
})();

(async()=>{
 const {$,request,money,esc,message}=window.POS;
 let rows=[];
 const body=$('pos-sales');
 const draw=()=>{
  const q=$('pos-search').value.trim().toLowerCase();
  const list=rows.filter(r=>[r.id,r.customer_name,r.status,r.notes,r.created_at].join(' ').toLowerCase().includes(q));
  body.innerHTML=list.length?list.map(r=>`<tr><td>${Number(r.id)}</td><td>${esc(r.created_at)}</td><td>${esc(r.customer_name||'Público general')}</td><td>${Number(r.cantidad_productos||0)}</td><td>S/ ${money(r.total)}</td><td>${esc(r.status)}</td><td>${esc(r.notes||'')}</td></tr>`).join(''):'<tr><td colspan="7">No se encontraron ventas.</td></tr>';
 };
 async function load(){try{rows=await request('get_ventas_recepcion');draw();}catch(e){message(e.message);}}
 $('pos-refresh').addEventListener('click',load); $('pos-search').addEventListener('input',draw); load();
})();
