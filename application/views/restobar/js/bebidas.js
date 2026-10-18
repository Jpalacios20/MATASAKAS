$(document).ready(function(){ cargarBebidas(); });
function cargarBebidas(){
  if ($.fn.DataTable.isDataTable('#datatable-restobar-bebidas')) $('#datatable-restobar-bebidas').DataTable().destroy();
  $('#datatable-restobar-bebidas').DataTable({ajax:{url:BASE_URL+'Restobar/get_bebidas',cache:false},columns:[
    {data:'product_sku',defaultContent:''},{data:'product_name',defaultContent:''},{data:'category',defaultContent:''},{data:'unit',defaultContent:''},
    {data:null,render:function(d){var p=parseFloat(d.price_sell||0); if(p<=0)p=parseFloat(d.product_price||0); return 'S/ '+p.toFixed(2);}},
    {data:'product_stock',defaultContent:'0'}, {data:'expiration_date',defaultContent:''},
    {data:'status',render:function(d){return d==1?'<span class="badge rounded-pill badge-light-success">Activo</span>':'<span class="badge rounded-pill badge-light-secondary">Inactivo</span>';}}
  ],dom:functions.head_datatable(),language:{url:BASE_URL+'public/assets/json/languaje-es.json'}});
}
