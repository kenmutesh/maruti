function initSparkline() {
    $('.sparkline').each(function () {
      var a = $(this);
      a.sparkline('html', a.data())
    })
  }
  function initCounters() {
    $('.count-to').countTo()
  }
  function skinChanger() {
    $('.right-sidebar .choose-skin li').on('click', function () {
      var a = $('body'),
      b = $(this),
      c = $('.right-sidebar .choose-skin li.active').data('theme');
      $('.right-sidebar .choose-skin li').removeClass('active'),
      a.removeClass('theme-' + c),
      b.addClass('active'),
      a.addClass('theme-' + b.data('theme'))
    })
  }
  function CustomScrollbar() {
    $('.right_menu .slim_scroll').slimscroll({
      height: 'calc(100vh - 30px)',
      color: 'rgba(0,0,0,0.1)',
      position: 'right',
      size: '2px',
      alwaysVisible: !1,
      borderRadius: '3px',
      railBorderRadius: '0'
    }),
    $('.cwidget-scroll').slimscroll({
      height: '306px',
      color: 'rgba(0,0,0,0.4)',
      size: '2px',
      alwaysVisible: !1,
      borderRadius: '3px',
      railBorderRadius: '2px'
    }),
    $('.right-sidebar .slim_scroll').slimscroll({
      height: 'calc(100vh - 100px)',
      color: 'rgba(0,0,0,0.4)',
      size: '2px',
      alwaysVisible: !1,
      borderRadius: '3px',
      railBorderRadius: '0'
    })
  }
  function CustomPageJS() {
    $('.boxs-close').on('click', function () {
      $(this).parents('.card').addClass('closed').fadeOut()
    }),
    $('.theme-light-dark .t-dark').on('click', function () {
      $('body').toggleClass('menu_dark')
    }),
    $('.menu-sm').on('click', function () {
      $('body').toggleClass('menu_sm')
    }),
    $('.minileftbar .notifications').on('click', function () {
      $('.right_menu .notif-menu').toggleClass('open stretchRight').siblings().removeClass('open stretchRight'),
      $('.right_menu .notif-menu').hasClass('open') ? $('.overlay').fadeIn() : $('.overlay').fadeOut()
    }),
    $('.minileftbar .task').on('click', function () {
      $('.right_menu .task-menu').toggleClass('open stretchRight').siblings().removeClass('open stretchRight'),
      $('.right_menu .task-menu').hasClass('open') ? $('.overlay').fadeIn() : $('.overlay').fadeOut()
    }),
    $('.minileftbar .menuapp-btn').on('click', function () {
      $('.right_menu .menu-app').toggleClass('open stretchRight').siblings().removeClass('open stretchRight'),
      $('.right_menu .menu-app').hasClass('open') ? $('.overlay').fadeIn() : $('.overlay').fadeOut()
    }),
    $('.minileftbar .js-right-sidebar').on('click', function () {
      $('.right_menu #rightsidebar').toggleClass('open stretchRight').siblings().removeClass('open stretchRight'),
      $('.right_menu #rightsidebar').hasClass('open') ? $('.overlay').fadeIn() : $('.overlay').fadeOut()
    }),
    $('.minileftbar .bars').on('click', function () {
      $('.right_menu .sidebar').toggleClass('open stretchRight').siblings().removeClass('open stretchRight'),
      $('.right_menu .sidebar').hasClass('open stretchRight') ? $('.overlay').fadeIn() : $('.overlay').fadeOut()
    }),
    $('.overlay').on('click', function () {
      $('.open.stretchRight').removeClass('open stretchRight'),
      $(this).fadeOut()
    }),
    $('.btn_overlay').on('click', function () {
      $('.overlay_menu').fadeToggle(200),
      $(this).toggleClass('btn-open').toggleClass('btn-close')
    }),
    $('.overlay_menu .btn').on('click', function () {
      $('.overlay_menu').fadeToggle(200),
      $('.overlay_menu button.btn').toggleClass('btn-open').toggleClass('btn-close'),
      open = !1
    }),
    $('.form-control').on('focus', function () {
      $(this).parent('.input-group').addClass('input-group-focus')
    }).on('blur', function () {
      $(this).parent('.input-group').removeClass('input-group-focus')
    })
  }
  if ('undefined' == typeof jQuery) throw new Error('jQuery plugins need to be before this file');
  $(function () {
    'use strict';
    $.AdminAlpino.browser.activate(),
    $.AdminAlpino.leftSideBar.activate(),
    $.AdminAlpino.select.activate(),
    setTimeout(function () {
      $('.page-loader-wrapper').fadeOut()
    }, 50)
  }),
  $.AdminAlpino = {
  },
  $.AdminAlpino.options = {
    colors: {
      red: '#ec3b57',
      pink: '#E91E63',
      purple: '#ba3bd0',
      deepPurple: '#673AB7',
      indigo: '#3F51B5',
      blue: '#2196f3',
      lightBlue: '#03A9F4',
      cyan: '#00bcd4',
      green: '#4CAF50',
      lightGreen: '#8BC34A',
      yellow: '#ffe821',
      orange: '#FF9800',
      deepOrange: '#f83600',
      grey: '#9E9E9E',
      blueGrey: '#607D8B',
      black: '#000000',
      blush: '#dd5e89',
      white: '#ffffff'
    },
    leftSideBar: {
      scrollColor: 'rgba(0,0,0,0.5)',
      scrollWidth: '4px',
      scrollAlwaysVisible: !1,
      scrollBorderRadius: '0',
      scrollRailBorderRadius: '0'
    }
  },
  $.AdminAlpino.leftSideBar = {
    activate: function () {
      var a = this,
      b = $('body'),
      c = $('.overlay');
      $(window).on('click', function (d) {
        var e = $(d.target);
        'i' === d.target.nodeName.toLowerCase() && (e = $(d.target).parent()),
        !e.hasClass('bars') && a.isOpen() && 0 === e.parents('#leftsidebar').length && (e.hasClass('js-right-sidebar') || c.fadeOut(), b.removeClass('overlay-open'))
      }),
      $.each($('.menu-toggle.toggled'), function (a, b) {
        $(b).next().slideToggle(0)
      }),
      $.each($('.menu .list li.active'), function (a, b) {
        var c = $(b).find('a:eq(0)');
        c.addClass('toggled'),
        c.next().show()
      }),
      $('.menu-toggle').on('click', function (a) {
        var b = $(this),
        c = b.next();
        if ($(b.parents('ul') [0]).hasClass('list')) {
          var d = $(a.target).hasClass('menu-toggle') ? a.target : $(a.target).parents('.menu-toggle');
          $.each($('.menu-toggle.toggled').not(d).next(), function (a, b) {
            $(b).is(':visible') && ($(b).prev().toggleClass('toggled'), $(b).slideUp())
          })
        }
        b.toggleClass('toggled'),
        c.slideToggle(320)
      }),
      a.checkStatuForResize(!0),
      $(window).resize(function () {
        a.checkStatuForResize(!1)
      }),
      Waves.attach('.menu .list a', [
        'waves-block'
      ]),
      Waves.init()
    },
    checkStatuForResize: function (a) {
      var b = $('body'),
      c = $('.minileftbar .menu_list .bars'),
      d = b.width();
      a && b.find('.content, .sidebar').addClass('no-animate').delay(1000).queue(function () {
        $(this).removeClass('no-animate').dequeue()
      }),
      d < 1170 ? (b.addClass('ls-closed'), c.fadeIn()) : (b.removeClass('ls-closed'), c.fadeOut())
    },
    isOpen: function () {
      return $('body').hasClass('overlay-open')
    }
  },
  $.AdminAlpino.select = {
    activate: function () {
      $.fn.selectpicker && $('select:not(.ms)').selectpicker()
    }
  };
  var edge = 'Microsoft Edge',
  ie10 = 'Internet Explorer 10',
  ie11 = 'Internet Explorer 11',
  opera = 'Opera',
  firefox = 'Mozilla Firefox',
  chrome = 'Google Chrome',
  safari = 'Safari';
  $.AdminAlpino.browser = {
    activate: function () {
      var a = this;
      '' !== a.getClassName() && $('html').addClass(a.getClassName())
    },
    getBrowser: function () {
      var a = navigator.userAgent.toLowerCase();
      return /edge/i.test(a) ? edge : /rv:11/i.test(a) ? ie11 : /msie 10/i.test(a) ? ie10 : /opr/i.test(a) ? opera : /chrome/i.test(a) ? chrome : /firefox/i.test(a) ? firefox : navigator.userAgent.match(/Version\/[\d\.]+.*Safari/) ? safari : void 0
    },
    getClassName: function () {
      var a = this.getBrowser();
      return a === edge ? 'edge' : a === ie11 ? 'ie11' : a === ie10 ? 'ie10' : a === opera ? 'opera' : a === chrome ? 'chrome' : a === firefox ? 'firefox' : a === safari ? 'safari' : ''
    }
  },
  $(function () {
    'use strict';
    skinChanger(),
    CustomScrollbar(),
    initSparkline(),
    initCounters(),
    CustomPageJS()
  });
  $(function () {
    'use strict';
    function a() {
      var a = screenfull.element;
      $('#status').text('Is fullscreen: ' + screenfull.isFullscreen),
      a && $('#element').text('Element: ' + a.localName + (a.id ? '#' + a.id : '')),
      screenfull.isFullscreen || ($('#external-iframe').remove(), document.body.style.overflow = 'auto')
    }
    if ($('#supported').text('Supported/allowed: ' + !!screenfull.enabled), !screenfull.enabled) return !1;
    $('#request').on('click', function () {
      screenfull.request($('#container') [0])
    }),
    $('#exit').on('click', function () {
      screenfull.exit()
    }),
    $('[data-provide~="boxfull"]').on('click', function () {
      screenfull.toggle($('.box') [0])
    }),
    $('[data-provide~="fullscreen"]').on('click', function () {
      screenfull.toggle($('#container') [0])
    });
    var b = '[data-provide~="boxfull"]',
    b = '[data-provide~="fullscreen"]';
    $(b).each(function () {
      $(this).data('fullscreen-default-html', $(this).html())
    }),
    document.addEventListener(screenfull.raw.fullscreenchange, function () {
      screenfull.isFullscreen ? $(b).each(function () {
        $(this).addClass('is-fullscreen')
      }) : $(b).each(function () {
        $(this).removeClass('is-fullscreen')
      })
    }),
    screenfull.on('change', a),
    a()
  });
  