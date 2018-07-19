var mockData = [
  { name: '日进斗金', total: '100000.00' },
  { name: '道一格零售店', total: '5505.00' },
  { name: '亲子花街零售', total: '100000.00' },
  // { name: '兴元零售店', total: '170005.00' },
  // { name: '好便利零售店', total: '35505.00' },
  // { name: '兴元零售店', total: '170005.00' },
  // { name: '好便利零售店', total: '35505.00' },
  // { name: '兴元零售店', total: '170005.00' },
  // { name: '好便利零售店', total: '35505.00' }
];
$(function() {
  var listEle = $('.winners-list-content');
  for(let shop of mockData) {
    listEle.append(`
    <div class="winners-list-item">
      <div class="item-name">${shop.name}</div>
      <div class="item-total">${shop.total}</div>
    </div>
    `);
  }
});

