Vue.use(httpVueLoader);
Vue.use(Toasted);

new Vue({
  el: "#manage-store",
  components: {},
  data: function () {
    return {
      filter: null,
      allStores: [],
      fields: [
        { key: "id", label: "Id", sortable: true, sortDirection: "desc" },
        { key: "store_name", label: "Store Name", sortable: true },
        { key: "installed_date", label: "Installed Date", sortable: true },
        { key: "action", label: "Actions" }
      ],
      currentPage: 1,
      perPage: 10,
      totalRows: 0,
      pageOptions: [10, 20, 30],
      showLoading: false
    };
  },
  mounted: function () {
    this.getAllStores();
  },
  methods: {
    getAllStores: function () {
      var seft = this;
      $.ajax({
        url: "services.php",
        data: {
          action: "getAllStores"
        },
        dataType: "JSON",
        type: "GET"
      }).done(function (response) {
        seft.allStores = response;
      });
    },
    onFiltered(filteredItems) {
      var seft = this;
      // Trigger pagination to update the number of buttons/pages due to filtering
      seft.totalRows = filteredItems.length;
      seft.currentPage = 1;
    },

    updateDataCache: function (shop) {
      var seft = this;
      seft.allStores.forEach(element => {
        if (element.store_name == shop) {
          element.status = 1;
        }
      });
      $.ajax({
        url: "services.php",
        data: {
          action: "updateDataCache",
          shop: shop
        },
        dataType: "JSON",
        type: "GET"
      }).done(function (response) {
        seft.allStores.forEach(element => {
          if (element.store_name == shop) {
            element.status = 0;
          }
        });
        if (response == true) {
          seft.$toasted.show("success!", {
            type: "success",
            duration: 3000
          });
        } else {
          seft.$toasted.show("error!", {
            type: "error",
            duration: 3000
          });
        }
      });
    },

    updateAllStore: function () {
      var seft = this;
      seft.showLoading = true;
      $.ajax({
        url: "services.php",
        data: {
          action: "updateAllStore"
        },
        dataType: "JSON",
        type: "GET"
      }).done(function (response) {
        seft.showLoading = false;
        if (response == true) {
          seft.$toasted.show("success!", {
            type: "success",
            duration: 3000
          });
        } else {
          seft.$toasted.show("error!", {
            type: "error",
            duration: 3000
          });
        }
      });
    }
  }
});
