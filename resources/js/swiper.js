'use strict';

function initCards(card, index) {
  var newCards = document.querySelectorAll('.swiper--card:not(.removed)');

  if (!newCards.length) {
    window.activeModal.hide();
  }

  newCards.forEach(function (card, index) {
    card.style.zIndex = window.allCards.length - index;
    card.style.transform = 'scale(' + (20 - index) / 20 + ') translateY(-' + 30 * index + 'px)';
    card.style.opacity = (10 - index) / 10;
  });

  window.swiperContainer.classList.add('loaded');
}

function generateCards() {
  window.allCards.forEach(function (el) {
    var hammertime = new Hammer(el);
  
    hammertime.on('pan', function (event) {
      el.classList.add('moving');
    });
  
    hammertime.on('pan', function (event) {
      if (event.deltaX === 0) return;
      if (event.center.x === 0 && event.center.y === 0) return;
  
      window.swiperContainer.classList.toggle('swiper_love', event.deltaX > 0);
      window.swiperContainer.classList.toggle('swiper_nope', event.deltaX < 0);
  
      var xMulti = event.deltaX * 0.03;
      var yMulti = event.deltaY / 80;
      var rotate = xMulti * yMulti;
  
      event.target.closest('.swiper--card').style.transform = 'translate(' + event.deltaX + 'px, ' + event.deltaY + 'px) rotate(' + rotate + 'deg)';
    });
  
    hammertime.on('panend', function (event) {
      el.classList.remove('moving');
      window.swiperContainer.classList.remove('swiper_love');
      window.swiperContainer.classList.remove('swiper_nope');
  
      var moveOutWidth = document.body.clientWidth;
      var keep = Math.abs(event.deltaX) < 80 || Math.abs(event.velocityX) < 0.5;
  
      event.target.closest('.swiper--card').classList.toggle('removed', !keep);
  
      if (keep) {
        event.target.closest('.swiper--card').style.transform = '';
      } else {
        var endX = Math.max(Math.abs(event.velocityX) * moveOutWidth, moveOutWidth);
        var toX = event.deltaX > 0 ? endX : -endX;
        var endY = Math.abs(event.velocityY) * moveOutWidth;
        var toY = event.deltaY > 0 ? endY : -endY;
        var xMulti = event.deltaX * 0.03;
        var yMulti = event.deltaY / 80;
        var rotate = xMulti * yMulti;

        // check if the card was liked or disliked
        if (event.deltaX > 0) {
          saveCard(event.target.closest('.swiper--card'));
        }
        
        event.target.closest('.swiper--card').style.transform = 'translate(' + toX + 'px, ' + (toY + event.deltaY) + 'px) rotate(' + rotate + 'deg)';
        initCards();
      }
    });
  });
}

function createButtonListener(love) {
  return function (event) {
    var cards = document.querySelectorAll('.swiper--card:not(.removed)');
    var moveOutWidth = document.body.clientWidth * 1.5;

    if (!cards.length) return false;

    var card = cards[0];

    card.classList.add('removed');

    if (love) {
      card.style.transform = 'translate(' + moveOutWidth + 'px, -100px) rotate(-30deg)';
      saveCard(card);
    } else {
      card.style.transform = 'translate(-' + moveOutWidth + 'px, -100px) rotate(30deg)';
    }

    initCards();

    event.preventDefault();
  };
}

function saveCard(card) {
  fetch('/swiper/' + card.dataset.id + '/accept', {
    method: 'POST',
    headers: {
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    }
  })
  .then(function() {
    window.refreshMindmap();
  });
}

window.generateSwiper = function () {
  window.swiperContainer = document.querySelector('.swiper');
  window.allCards = document.querySelectorAll('.swiper--card');

  generateCards();
  initCards();

  const nope = document.querySelectorAll('.reject');
  const love = document.querySelectorAll('.accept');

  var nopeListener = createButtonListener(false);
  var loveListener = createButtonListener(true);
  
  nope.forEach(function (el) {
      el.addEventListener('click', nopeListener);
  });
  
  love.forEach(function (el) {
      el.addEventListener('click', loveListener);
  });
}