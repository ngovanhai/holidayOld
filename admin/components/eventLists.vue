<template>
  <div class="md-layout md-gutter">
    <b-loading :is-full-page="true" :active.sync="isFetching" :can-cancel="false"></b-loading>
    <div class="md-layout-item md-size-100">
      <b-table
        :data="events"
        :bordered="true"
        :striped="true"
        :mobile-cards="true"
        :paginated="isPaginated"
        :per-page="perPage"
        :current-page.sync="currentPage"
      >
        <template slot-scope="props">
          <b-table-column field="event_name" label="Event title">{{ props.row.event_name }}</b-table-column>
          <b-table-column field="start_date" label="Start date">{{ props.row.start_date }}</b-table-column>
          <b-table-column field="end_date" label="End date">{{ props.row.end_date }}</b-table-column>
          <b-table-column field="action" label="Action">
            <md-button
              v-if="props.row.publish == '0'"
              class="md-icon-button md-primary"
              @click="publishEvent(props.row.id)"
            >
              <md-icon>play_circle_outline</md-icon>
              <md-tooltip md-direction="bottom">Publish Event</md-tooltip>
            </md-button>
            <md-button
              v-if="props.row.publish == '1'"
              class="md-icon-button md-primary"
              @click="unpublishEvent(props.row.id)"
            >
              <md-icon>pause_circle_outline</md-icon>
              <md-tooltip md-direction="bottom">Unpublish Event</md-tooltip>
            </md-button>
            <md-button class="md-icon-button md-primary" @click="editEvent(props.row)">
              <md-icon>edit</md-icon>
              <md-tooltip md-direction="bottom">Edit</md-tooltip>
            </md-button>
            <md-button class="md-icon-button md-primary" @click="deleteEvent(props.row.id)">
              <md-icon>delete</md-icon>
              <md-tooltip md-direction="bottom">Delete</md-tooltip>
            </md-button>
          </b-table-column>
        </template>
        <template slot="empty">
          <section class="section">
            <div class="content has-text-grey has-text-centered">No events found</div>
          </section>
        </template>
      </b-table>
    </div>
    <md-dialog :md-active.sync="newDialog" id="event-dialog">
      <md-dialog-title>New Event</md-dialog-title>
      <md-content class="md-dialog-content md-scrollbar">
        <create-event
          :choose-icons="chooseIcons"
          :choose-images="chooseImages"
          :booktake-images="booktakeImages"
          :choose-frames="chooseFrames"
          :custom-images="customImages"
          v-on:close-dialog="closeDialog"
          v-on:reload-events="reloadEvents"
        ></create-event>
      </md-content>
    </md-dialog>
    <md-dialog :md-active.sync="editDialog" id="event-dialog">
      <md-dialog-title>Edit {{ currentEvent.event_name }} Event</md-dialog-title>
      <md-content class="md-dialog-content md-scrollbar">
        <edit-event
          :choose-icons="chooseIcons"
          :choose-images="chooseImages"
          :booktake-images="booktakeImages"
          :choose-frames="chooseFrames"
          :current-event="currentEvent"
          :custom-images="customImages"
          v-on:close-dialog="closeDialog"
          v-on:reload-events="reloadEvents"
        ></edit-event>
      </md-content>
    </md-dialog>
    <md-dialog :md-active.sync="customDialog" id="images-dialog">
      <md-dialog-title>Shop's custom images</md-dialog-title>
      <md-content class="md-dialog-content md-scrollbar">
        <shop-images
          :custom-images="customImages"
          v-on:close-dialog="closeDialog"
          v-on:reload-images="getShopCustomImages"
        ></shop-images>
      </md-content>
    </md-dialog>
  </div>
</template>
 
<script>
const shop = window.shop;
const rootLink = window.rootLink;

module.exports = {
  props: ["chooseIcons", "chooseImages", "booktakeImages", "chooseFrames"],
  data: function() {
    return {
      events: [],
      currentEvent: {},
      newDialog: false,
      editDialog: false,
      customDialog: false,
      customImages: [],
      config: {
        enableTime: true,
        dateFormat: "m/d/Y H:i"
      },
      isFetching: true,
      isPaginated: false,
      perPage: 10,
      currentPage: 1
    };
  },
  mounted: function() {
    var self = this;
    self.getAllEvents();
    self.getShopCustomImages();
    ShopifyApp.ready(function() {
      ShopifyApp.Bar.initialize({
        buttons: {
          secondary: [
            {
              label: "Create event",
              callback: function() {
                self.newDialog = true;
                self.editDialog = false;
                self.customDialog = false;
              }
            },
            {
              label: "Manage your images",
              callback: function() {
                self.customDialog = true;
                self.editDialog = false;
                self.newDialog = false;
              }
            }
          ]
        },
        title: "Manage Events"
      });
    });
  },
  methods: {
    getAllEvents: function() {
      var self = this;
      self.isFetching = true;
      self.$http
        .get("services.php", {
          params: {
            action: "getAllEvents",
            shop: shop
          }
        })
        .then(result => {
          self.isFetching = false;
          self.events = result.body.map(event => {
            event.choose_icons =
              event.choose_icons != "" ? JSON.parse(event.choose_icons) : "";
            event.choose_images =
              event.choose_images != "" ? JSON.parse(event.choose_images) : "";
            event.custom_images =
              event.custom_images != "" ? JSON.parse(event.custom_images) : "";
            event.only_home == 1
              ? (event.only_home = true)
              : (event.only_home = false);
            return event;
          });
          console.log(self.events);
        });
    },
    getShopCustomImages: function() {
      var self = this;
      self.$http
        .get("services.php", {
          params: {
            action: "getShopCustomImages",
            shop: shop
          }
        })
        .then(result => {
          self.customImages = result.body;
        });
    },
    editEvent: function(event) {
      var self = this;
      self.currentEvent = event;
      self.editDialog = true;
    },
    publishEvent: function(id) {
      var self = this;
      self.$http
        .post(
          "services.php",
          {
            action: "publishEvent",
            shop: shop,
            id: id
          },
          {
            emulateJSON: true
          }
        )
        .then(() => {
          ShopifyApp.flashNotice("Publish event successful!");
          self.getAllEvents();
        });
    },
    unpublishEvent: function(id) {
      var self = this;
      self.$http
        .post(
          "services.php",
          {
            action: "unpublishEvent",
            shop: shop,
            id: id
          },
          {
            emulateJSON: true
          }
        )
        .then(() => {
          ShopifyApp.flashNotice("Unpublish event successful!");
          self.getAllEvents();
        });
    },
    deleteEvent: function(id) {
      var self = this;
      ShopifyApp.Modal.confirm(
        {
          title: "Delete event?",
          message:
            "Are you sure you want to delete this event? This action cannot be undone.",
          okButton: "Agree",
          cancelButton: "Disagree",
          style: "danger"
        },
        function(result) {
          if (result) {
            self.$http
              .post(
                "services.php",
                {
                  action: "deleteEvent",
                  shop: shop,
                  id: id
                },
                {
                  emulateJSON: true
                }
              )
              .then(() => {
                ShopifyApp.flashNotice("Delete event successful!");
                self.getAllEvents();
              });
          }
        }
      );
    },
    reloadEvents: function() {
      var self = this;
      self.editDialog = false;
      self.newDialog = false;
      self.currentEvent = {};
      self.getAllEvents();
    },
    closeDialog: function() {
      var self = this;
      self.editDialog = false;
      self.newDialog = false;
      self.customDialog = false;
    }
  },
  components: {
    "flat-pickr": VueFlatpickr,
    "create-event": httpVueLoader(
      `${rootLink}/admin/components/createEvent.vue?v=1`
    ),
    "edit-event": httpVueLoader(
      `${rootLink}/admin/components/editEvent.vue?v=1`
    ),
    "shop-images": httpVueLoader(
      `${rootLink}/admin/components/shopImages.vue?v=1`
    )
  }
};
</script>
<style>
.dropzone {
  min-height: 170px;
  max-width: 200px;
  padding: 10px;
  text-align: center;
}
.vue-dropzone .dz-preview .dz-image {
  height: auto;
}
.vue-dropzone .dz-preview .dz-remove {
  margin-left: 0;
  padding: 0;
  border-width: 1px;
  font-size: 12px;
  bottom: 30px;
}
.vue-dropzone .dz-preview .dz-remove:hover {
  color: #fff;
}
</style>