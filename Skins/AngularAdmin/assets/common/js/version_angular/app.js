'use strict';
var TEMPLATE_PATH = '/Skins/AngularAdmin/';
angular.module('cleanUI', [
    'ngRoute',
    'cleanUI.controllers'
])
.config(['$locationProvider', '$routeProvider',
    function($locationProvider, $routeProvider) {
        /////////////////////////////////////////////////////////////
        // SYSTEM
        $routeProvider.when('/', {redirectTo: '/dashboards/alpha'});
        $routeProvider.otherwise({redirectTo: 'pages/page-404'});

        /////////////////////////////////////////////////////////////
        // Documentation
        $routeProvider.when('/documentation/index', {
            templateUrl: TEMPLATE_PATH+'documentation/index.html'
        });

        /////////////////////////////////////////////////////////////
        // Dashboards
        $routeProvider.when('/dashboards/alpha', {
            templateUrl: TEMPLATE_PATH+'dashboards/alpha.html'
        });

        $routeProvider.when('/dashboards/beta', {
            templateUrl: TEMPLATE_PATH+'dashboards/beta.html'
        });

        /////////////////////////////////////////////////////////////
        // Apps
        $routeProvider.when('/apps/profile', {
            templateUrl: TEMPLATE_PATH+'apps/profile.html'
        });

        $routeProvider.when('/apps/messaging', {
            templateUrl: TEMPLATE_PATH+'apps/messaging.html'
        });

        $routeProvider.when('/apps/mail', {
            templateUrl: TEMPLATE_PATH+'apps/mail.html'
        });

        $routeProvider.when('/apps/calendar', {
            templateUrl: TEMPLATE_PATH+'apps/calendar.html'
        });

        $routeProvider.when('/apps/gallery', {
            templateUrl: TEMPLATE_PATH+'apps/gallery.html'
        });

        /////////////////////////////////////////////////////////////
        // Ecommerce
        $routeProvider.when('/ecommerce/cart-checkout', {
            templateUrl: TEMPLATE_PATH+'ecommerce/cart-checkout.html'
        });

        $routeProvider.when('/ecommerce/dashboard', {
            templateUrl: TEMPLATE_PATH+'ecommerce/dashboard.html'
        });

        $routeProvider.when('/ecommerce/orders', {
            templateUrl: TEMPLATE_PATH+'ecommerce/orders.html'
        });

        $routeProvider.when('/ecommerce/product-details', {
            templateUrl: TEMPLATE_PATH+'ecommerce/product-details.html'
        });

        $routeProvider.when('/ecommerce/product-edit', {
            templateUrl: TEMPLATE_PATH+'ecommerce/product-edit.html'
        });

        $routeProvider.when('/ecommerce/products-list', {
            templateUrl: TEMPLATE_PATH+'ecommerce/products-list.html'
        });

        $routeProvider.when('/ecommerce/products-catalog', {
            templateUrl: TEMPLATE_PATH+'ecommerce/products-catalog.html'
        });

        /////////////////////////////////////////////////////////////
        // Layout
        $routeProvider.when('/layout/grid', {
            templateUrl: TEMPLATE_PATH+'layout/grid.html'
        });

        $routeProvider.when('/layout/panels', {
            templateUrl: TEMPLATE_PATH+'layout/panels.html'
        });

        $routeProvider.when('/layout/sidebars', {
            templateUrl: TEMPLATE_PATH+'layout/sidebars.html'
        });

        $routeProvider.when('/layout/utilities', {
            templateUrl: TEMPLATE_PATH+'layout/utilities.html'
        });

        $routeProvider.when('/layout/typography', {
            templateUrl: TEMPLATE_PATH+'layout/typography.html'
        });

        /////////////////////////////////////////////////////////////
        // Icons
        $routeProvider.when('/icons/fontawesome', {
            templateUrl: TEMPLATE_PATH+'icons/fontawesome.html'
        });

        $routeProvider.when('/icons/icomoon-ultimate', {
            templateUrl: TEMPLATE_PATH+'icons/icomoon-ultimate.html'
        });

        /////////////////////////////////////////////////////////////
        // Forms
        $routeProvider.when('/forms/autocomplete', {
            templateUrl: TEMPLATE_PATH+'forms/autocomplete.html'
        });

        $routeProvider.when('/forms/basic-form-elements', {
            templateUrl: TEMPLATE_PATH+'forms/basic-form-elements.html'
        });

        $routeProvider.when('/forms/buttons', {
            templateUrl: TEMPLATE_PATH+'forms/buttons.html'
        });

        $routeProvider.when('/forms/checkboxes-radio', {
            templateUrl: TEMPLATE_PATH+'forms/checkboxes-radio.html'
        });

        $routeProvider.when('/forms/dropdowns', {
            templateUrl: TEMPLATE_PATH+'forms/dropdowns.html'
        });

        $routeProvider.when('/forms/extras', {
            templateUrl: TEMPLATE_PATH+'forms/extras.html'
        });

        $routeProvider.when('/forms/form-wizard', {
            templateUrl: TEMPLATE_PATH+'forms/form-wizard.html'
        });

        $routeProvider.when('/forms/form-validation', {
            templateUrl: TEMPLATE_PATH+'forms/form-validation.html'
        });

        $routeProvider.when('/forms/input-mask', {
            templateUrl: TEMPLATE_PATH+'forms/input-mask.html'
        });

        $routeProvider.when('/forms/file-uploads', {
            templateUrl: TEMPLATE_PATH+'forms/file-uploads.html'
        });

        $routeProvider.when('/forms/selectboxes', {
            templateUrl: TEMPLATE_PATH+'forms/selectboxes.html'
        });


        /////////////////////////////////////////////////////////////
        // Components
        $routeProvider.when('/components/badges-labels', {
            templateUrl: TEMPLATE_PATH+'components/badges-labels.html'
        });

        $routeProvider.when('/components/calendar', {
            templateUrl: TEMPLATE_PATH+'components/calendar.html'
        });

        $routeProvider.when('/components/carousel', {
            templateUrl: TEMPLATE_PATH+'components/carousel.html'
        });

        $routeProvider.when('/components/collapse', {
            templateUrl: TEMPLATE_PATH+'components/collapse.html'
        });

        $routeProvider.when('/components/date-picker', {
            templateUrl: TEMPLATE_PATH+'components/date-picker.html'
        });

        $routeProvider.when('/components/media-players', {
            templateUrl: TEMPLATE_PATH+'components/media-players.html'
        });

        $routeProvider.when('/components/modal', {
            templateUrl: TEMPLATE_PATH+'components/modal.html'
        });

        $routeProvider.when('/components/nestable', {
            templateUrl: TEMPLATE_PATH+'components/nestable.html'
        });

        $routeProvider.when('/components/notifications-alerts', {
            templateUrl: TEMPLATE_PATH+'components/notifications-alerts.html'
        });

        $routeProvider.when('/components/pagination', {
            templateUrl: TEMPLATE_PATH+'components/pagination.html'
        });

        $routeProvider.when('/components/loading-progress', {
            templateUrl: TEMPLATE_PATH+'components/loading-progress.html'
        });

        $routeProvider.when('/components/progress-bars', {
            templateUrl: TEMPLATE_PATH+'components/progress-bars.html'
        });

        $routeProvider.when('/components/slider', {
            templateUrl: TEMPLATE_PATH+'components/slider.html'
        });

        $routeProvider.when('/components/steps', {
            templateUrl: TEMPLATE_PATH+'components/steps.html'
        });

        $routeProvider.when('/components/breadcrumbs', {
            templateUrl: TEMPLATE_PATH+'components/breadcrumbs.html'
        });

        $routeProvider.when('/components/tabs', {
            templateUrl: TEMPLATE_PATH+'components/tabs.html'
        });

        $routeProvider.when('/components/text-editor', {
            templateUrl: TEMPLATE_PATH+'components/text-editor.html'
        });

        $routeProvider.when('/components/mail-templates', {
            templateUrl: TEMPLATE_PATH+'components/mail-templates.html'
        });

        $routeProvider.when('/components/tooltips-popovers', {
            templateUrl: TEMPLATE_PATH+'components/tooltips-popovers.html'
        });

        /////////////////////////////////////////////////////////////
        // Tables
        $routeProvider.when('/tables/basic-tables', {
            templateUrl: TEMPLATE_PATH+'tables/basic-tables.html'
        });

        $routeProvider.when('/tables/datatables', {
            templateUrl: TEMPLATE_PATH+'tables/datatables.html'
        });

        $routeProvider.when('/tables/editable-tables', {
            templateUrl: TEMPLATE_PATH+'tables/editable-tables.html'
        });

        /////////////////////////////////////////////////////////////
        // Charts
        $routeProvider.when('/charts/c3', {
            templateUrl: TEMPLATE_PATH+'charts/c3.html'
        });

        $routeProvider.when('/charts/chartjs', {
            templateUrl: TEMPLATE_PATH+'charts/chartjs.html'
        });

        $routeProvider.when('/charts/d3', {
            templateUrl: TEMPLATE_PATH+'charts/d3.html'
        });

        $routeProvider.when('/charts/chartistjs', {
            templateUrl: TEMPLATE_PATH+'charts/chartistjs.html'
        });

        $routeProvider.when('/charts/peity', {
            templateUrl: TEMPLATE_PATH+'charts/peity.html'
        });


        /////////////////////////////////////////////////////////////
        // Pages
        $routeProvider.when('/pages/invoice', {
            templateUrl: TEMPLATE_PATH+'pages/invoice.html'
        });

        $routeProvider.when('/pages/lockscreen', {
            templateUrl: TEMPLATE_PATH+'pages/lockscreen.html',
            controller: 'lockscreenPageCtrl'
        });

        $routeProvider.when('/pages/login-alpha', {
            templateUrl: TEMPLATE_PATH+'pages/login-alpha.html',
            controller: 'loginPageCtrl'
        });

        $routeProvider.when('/pages/login-beta', {
            templateUrl: TEMPLATE_PATH+'pages/login-beta.html',
            controller: 'loginPageCtrl'
        });

        $routeProvider.when('/pages/login-omega', {
            templateUrl: TEMPLATE_PATH+'pages/login-omega.html',
            controller: 'loginPageCtrl'
        });

        $routeProvider.when('/pages/page-404', {
            templateUrl: TEMPLATE_PATH+'pages/page-404.html'
        });

        $routeProvider.when('/pages/page-500', {
            templateUrl: TEMPLATE_PATH+'pages/page-500.html'
        });

        $routeProvider.when('/pages/pricing-tables', {
            templateUrl: TEMPLATE_PATH+'pages/pricing-tables.html'
        });

        $routeProvider.when('/pages/register', {
            templateUrl: TEMPLATE_PATH+'pages/register.html',
            controller: 'registerPageCtrl'
        });

    }
]);

var app = angular.module('cleanUI.controllers', []);

app.controller('MainCtrl', function($location, $scope, $rootScope, $timeout) {

    NProgress.configure({
        minimum: 0.2,
        trickleRate: 0.1,
        trickleSpeed: 200
    });

    $scope.$on('$routeChangeStart', function() {

        // NProgress Start
        $('body').addClass('cui-page-loading-state');
        NProgress.start();

    });

    $scope.$on('$routeChangeSuccess', function() {

        // Set to default (show) state left and top menu, remove single page classes
        $('body').removeClass('single-page single-page-inverse');
        $rootScope.hideLeftMenu = false;
        $rootScope.hideTopMenu = false;
        $rootScope.showFooter = true;

        // Firefox issue: scroll top when page load
        $('html, body').scrollTop(0);

        // Set active state menu after success change route
        $('.left-menu-list-active').removeClass('left-menu-list-active');
        $('nav.left-menu .left-menu-list-root .left-menu-link').each(function(){
            if ($(this).attr('href') == '#' + $location.path()) {
                $(this).closest('.left-menu-list-root > li').addClass('left-menu-list-active');
            }
        });

        // NProgress End
        setTimeout(function(){
            NProgress.done();
        }, 1000);
        $('body').removeClass('cui-page-loading-state');
    });

});

app.directive('leftMenu', function() {
    return {
        restrict: 'A',
        link: function(scope, element, attrs) {
            element.on('click', '.left-menu-link', function() {

                if (!$(this).closest('.left-menu-list-submenu').length) {
                    $('.left-menu-list-opened > a + ul').slideUp(200, function(){
                        $('.left-menu-list-opened').removeClass('left-menu-list-opened');
                    });
                }

            });
        }
    };
});