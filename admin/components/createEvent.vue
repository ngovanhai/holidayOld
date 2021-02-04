<template>
  <div class="md-layout md-gutter">
    <b-loading :is-full-page="true" :active.sync="isCreating" :can-cancel="false"></b-loading>
    <div class="md-layout-item md-size-100">
      <div class="md-layout md-gutter">
        <div class="md-layout-item md-size-50 md-xsmall-size-100">
          <md-field>
            <label>Event's title</label>
            <md-input
              v-model="newevent.event_name"
              v-on:keyup="enableAddNewEventButton($event.target)"
            ></md-input>
          </md-field>
        </div>
        <div class="md-layout-item md-size-50 md-xsmall-size-100">
          <md-field>
            <label>Total of icons/images</label>
            <md-select v-model="newevent.number_of_icons">
              <md-option value="10">10</md-option>
              <md-option value="20">20</md-option>
              <md-option value="30">40</md-option>
              <md-option value="50">50</md-option>
            </md-select>
          </md-field>
        </div>
      </div>
    </div>
    <div class="md-layout-item md-size-100">
      <div class="md-layout md-gutter">
        <div class="md-layout-item md-size-50 md-xsmall-size-100">
          <md-field>
            <label>Animation speed</label>
            <md-select v-model="newevent.animation_speed">
              <md-option value="11">Slow</md-option>
              <md-option value="7">Normal</md-option>
              <md-option value="3">Fast</md-option>
            </md-select>
          </md-field>
        </div>
        <div class="md-layout-item md-size-50 md-xsmall-size-100">
          <md-field>
            <label>Icon size</label>
            <md-select v-model="newevent.icon_size">
              <md-option value="10">Small</md-option>
              <md-option value="20">Medium</md-option>
              <md-option value="30">Large</md-option>
            </md-select>
          </md-field>
        </div>
      </div>
    </div>
    <div class="md-layout-item md-size-100">
      <div class="md-layout md-gutter">
        <div class="md-layout-item md-size-50 md-xsmall-size-100">
          <label>Icon color</label>
          <div>
            <input type="color" class="inputColor" v-model="newevent.icon_color" />
          </div>
        </div>
        <div class="md-layout-item md-size-50 md-xsmall-size-100">
          <md-field>
            <label>Image size</label>
            <md-select v-model="newevent.image_size">
              <md-option value="20">Small</md-option>
              <md-option value="40">Medium</md-option>
              <md-option value="60">Large</md-option>
            </md-select>
          </md-field>
        </div>
      </div>
    </div>
    <div class="md-layout-item md-size-100">
      <div class="md-layout md-gutter">
        <div class="md-layout-item md-size-50 md-xsmall-size-100">
          <label>Time to keep icons and images effect on store- keep 0 value for forever</label>
          <div>
            <input class="form-control" v-model="newevent.effect_time" type="number" min="0" />
            <span class="prefix-input">seconds</span>
          </div>
        </div>
        <div class="md-layout-item md-size-50 md-xsmall-size-100">
          <label>Time to keep frame on store</label>
          <div>
            <input class="form-control" v-model="newevent.frame_time" type="number" min="0" />
            <span class="prefix-input">seconds</span>
          </div>
        </div>
      </div>
    </div>
    <div class="md-layout-item md-size-100">
      <div class="md-layout md-gutter">
        <div class="md-layout-item md-size-50 md-xsmall-size-100">
          <md-checkbox v-model="newevent.only_home"></md-checkbox>&nbsp;
          <label>Only at homepage</label>
        </div>
        <div class="md-layout-item md-size-50 md-xsmall-size-100">
          <md-field>
            <label>Show frame on</label>
            <md-select v-model="newevent.frame_position">
              <md-option value="1">Only top</md-option>
              <md-option value="2">Only bottom</md-option>
              <md-option value="3">Both top and bottom</md-option>
            </md-select>
          </md-field>
        </div>
      </div>
    </div>
    <div class="md-layout-item md-size-100">
      <div class="md-layout md-gutter">
        <div class="md-layout-item md-size-50 md-xsmall-size-100">
          <label>Start date</label>
          <div>
            <flat-pickr
              v-model="newevent.start_date"
              :config="flatConfig"
              class="form-control"
              placeholder="Select a date"
            ></flat-pickr>
          </div>
        </div>
        <div class="md-layout-item md-size-50 md-xsmall-size-100">
          <label>End date</label>
          <div>
            <flat-pickr
              v-model="newevent.end_date"
              :config="flatConfig"
              class="form-control"
              placeholder="Select a date"
            ></flat-pickr>
          </div>
        </div>
      </div>
    </div>
    <div class="md-layout-item md-size-100">
      <h4>Choose icons</h4>
      <div class="md-layout md-gutter chooseIcons">
        <div
          class="md-layout-item md-size-10 md-xsmall-size-20"
          v-for="item of chooseIcons"
          :key="item.id"
        >
          <md-checkbox v-model="newevent.choose_icons" :value="item.id">
            <i :class="item.class" aria-hidden="true"></i>
            <br />
            <small>{{ item.id }}</small>
          </md-checkbox>
        </div>
      </div>
    </div>
    <div class="md-layout-item md-size-100">
      <h4>Choose images</h4>
      <div class="md-layout md-gutter chooseImages">
        <div
          class="md-layout-item md-size-10 md-xsmall-size-20"
          v-for="item of chooseImages"
          :key="item.id"
        >
          <md-checkbox v-model="newevent.choose_images" :value="item.id">
            <img :src="item.image" alt />
            <br />
            <small>{{ item.id }}</small>
          </md-checkbox>
        </div>
        <div
          v-if="shopName == 'booktake.myshopify.com'"
          class="md-layout-item md-size-10 md-xsmall-size-20"
          v-for="item of booktakeImages"
          :key="item.id"
        >
          <md-checkbox v-model="newevent.choose_images" :value="item.id">
            <img :src="item.image" alt />
            <br />
            <small>{{ item.id }}</small>
          </md-checkbox>
        </div>
      </div>
    </div>
    <div class="md-layout-item md-size-100" v-if="customImages.length > 0">
      <h4>Your shop custom image</h4>
      <div class="md-layout md-gutter chooseImages">
        <div
          class="md-layout-item md-size-10 md-xsmall-size-20"
          v-for="item of customImages"
          :key="item.id"
        >
          <md-checkbox v-model="newevent.custom_images" :value="item.url">
            <img :src="item.url" alt />
            <br />
            <small>{{ item.name }}</small>
          </md-checkbox>
        </div>
      </div>
    </div>
    <div class="md-layout-item md-size-100">
      <h4>Choose frames</h4>
      <a class="undo" v-on:click="newevent.frames = ''">Clear frame's selection</a>
      <div class="md-layout md-gutter chooseFrames">
        <div
          class="md-layout-item md-size-20 md-small-size-25 md-xsmall-size-50"
          v-for="item of chooseFrames"
          :key="item.id"
        >
          <md-radio v-model="newevent.frames" :value="item.id">
            <img :src="item.image" alt />
            <br />
          </md-radio>
        </div>
      </div>
    </div>
    <div class="md-layout-item md-size-100">
      <md-button
        class="md-raised md-primary"
        id="add-event-button"
        v-on:click="addEvent()"
        disabled
      >Save</md-button>
      <md-button class="md-primary" @click="closeDialog">Close</md-button>
    </div>
  </div>
</template>
 
<script>
const shop = window.shop;
const rootLink = window.rootLink;

var date = new Date();
var day = date.getDate() < 10 ? "0" + date.getDate() : date.getDate();
var month =
  date.getMonth() + 1 < 10 ? "0" + (date.getMonth() + 1) : date.getMonth() + 1;
var year = date.getFullYear();
var hours = date.getHours() < 10 ? "0" + date.getHours() : date.getHours();
var minutes =
  date.getMinutes() < 10 ? "0" + date.getMinutes() : date.getMinutes();

module.exports = {
  props: [
    "chooseIcons",
    "chooseImages",
    "booktakeImages",
    "chooseFrames",
    "customImages"
  ],
  data: function() {
    return {
      newevent: {
        event_name: "",
        number_of_icons: 10,
        animation_speed: 7,
        icon_size: 30,
        image_size: 40,
        icon_color: "#002868",
        start_date:
          month + "/" + day + "/" + year + " " + hours + ":" + minutes,
        end_date: month + "/" + day + "/" + year + " " + hours + ":" + minutes,
        frames: "",
        choose_icons: [],
        choose_images: [],
        custom_images: [],
        effect_time: 0,
        frame_time: 20,
        frame_position: 1,
        only_home: false
      },
      flatConfig: {
        enableTime: true,
        dateFormat: "m/d/Y H:i"
      },
      isCreating: false,
      shopName: shop
    };
  },
  mounted: function() {
    $("#add-event-button").prop("disabled", true);
  },
  methods: {
    addEvent: function() {
      var self = this;
      self.isCreating = true;
      self.$http
        .post(
          "services.php",
          {
            action: "addEvent",
            shop: shop,
            newevent: self.newevent
          },
          {
            emulateJSON: true
          }
        )
        .then(() => {
          self.isCreating = false;
          ShopifyApp.flashNotice(
            `Create event : ${self.newevent.event_name} successfully !`
          );
          self.resetNewEvent();
          self.$emit("reload-events");
        });
    },
    enableAddNewEventButton: function(button) {
      if (button.value != "") {
        $("#add-event-button").removeAttr("disabled");
      } else {
        $("#add-event-button").prop("disabled", true);
      }
    },
    resetNewEvent: function() {
      var self = this;
      self.newevent = {
        event_name: "",
        number_of_icons: 10,
        animation_speed: 7,
        icon_size: 30,
        image_size: 40,
        icon_color: "#002868",
        start_date:
          month + "/" + day + "/" + year + " " + hours + ":" + minutes,
        end_date: month + "/" + day + "/" + year + " " + hours + ":" + minutes,
        frames: "",
        choose_icons: [],
        choose_images: [],
        custom_images: [],
        effect_time: 0,
        frame_time: 20,
        frame_position: 1,
        only_home: false
      };
    },
    closeDialog: function() {
      var self = this;
      self.$emit("close-dialog");
    }
  },
  components: {
    "flat-pickr": VueFlatpickr,
    vueDropzone: vue2Dropzone
  }
};
</script>