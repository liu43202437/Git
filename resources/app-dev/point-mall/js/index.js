$(function() {
  $('.carousel').carousel({
    interval: 5000
  });
  var mockData = [
    { img: '', point: 2000, title: '手帕纸' },
    { img: '', point: 5000, title: '手帕纸' }
  ];

  createProductList(mockData);

  function createProductList(list) {
    $('.items-box').html('');
    for (let product of list) {
      $('.items-box').append(`
        <div class="item">
          <div class="item-content">
            <img src="${product.img}" alt="">
            <div class="item-bottom">
              <div class="item-bottom-title">${product.title}</div>
              <div class="point-info">
                <img src="../images/point_icon.png" alt="" class="point-icon">
                <span>${product.point}公益分</span>
              </div>
            </div>
          </div>
        </div>
      `);
    }
  }
}());