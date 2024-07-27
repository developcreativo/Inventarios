Nova.booting((Vue, router, store) => {
  router.addRoutes([
    {
      name: 'inventarios',
      path: '/inventarios',
      component: require('./components/Tool'),
    },
  ])
  // Detail
  Vue.component('detail-broadcaster-field', require('./components/Detail/BroadcasterField'));
  Vue.component('detail-listener-field', require('./components/Detail/ListenerField'));
  Vue.component('detail-broadcaster-select-field', require('./components/Detail/BroadcasterSelectField'));
  Vue.component('detail-broadcaster-belongsto-field', require('./components/Detail/BroadcasterBelongsToField'));
  // Form
  Vue.component('form-broadcaster-field', require('./components/Form/BroadcasterField'));
  Vue.component('form-listener-field', require('./components/Form/ListenerField'));
  Vue.component('form-broadcaster-select-field', require('./components/Form/BroadcasterSelectField'));
  Vue.component('form-broadcaster-belongsto-field', require('./components/Form/BroadcasterBelongsToField'));
  // Index
  Vue.component('index-broadcaster-field', require('./components/Index/BroadcasterField'));
  Vue.component('index-listener-field', require('./components/Index/ListenerField'));
  Vue.component('index-broadcaster-select-field', require('./components/Index/BroadcasterSelectField'));
  Vue.component('index-broadcaster-belongsto-field', require('./components/Index/BroadcasterBelongsToField'));
})
