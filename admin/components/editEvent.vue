<template>
  <div class="md-layout md-gutter">
    <b-loading :is-full-page="true" :active.sync="isEditing" :can-cancel="false"></b-loading>
    <div class="md-layout-item md-size-100">
      <div class="md-layout md-gutter">
        <div class="md-layout-item md-size-50 md-xsmall-size-100">
          <md-field>
            <label>Event's title</label>
            <md-input
              v-model="currentEvent.event_name"
              v-on:keyup="enableEditEventButton($event.target)"
            ></md-input>
          </md-field>
        </div>
        <div class="md-layout-item md-size-50 md-xsmall-size-100">
          <md-field>
            <label>Total of icons/images</label>
            <md-select v-model="currentEvent.number_of_icons">
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
            <md-select v-model="currentEvent.animation_speed">
              <md-option value="11">Slow</md-option>
              <md-option value="7">Normal</md-option>
              <md-option value="3">Fast</md-option>
            </md-select>
          </md-field>
        </div>
        <div class="md-layout-item md-size-50 md-xsmall-size-100">
          <md-field>
            <label>Icon size</label>
            <md-select v-model="currentEvent.icon_size">
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
            <input type="color" class="inputColor" v-model="currentEvent.icon_color" />
          </div>
        </div>
        <div class="md-layout-item md-size-50 md-xsmall-size-100">
          <md-field>
            <label>Image size</label>
            <md-select v-model="currentEvent.image_size">
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
            <input class="form-control" v-model="currentEvent.effect_time" type="number" min="0" />
            <span class="prefix-input">seconds</span>
          </div>
        </div>
        <div class="md-layout-item md-size-50 md-xsmall-size-100">
          <label>Time to keep frame on store</label>
          <div>
            <input class="form-control" v-model="currentEvent.frame_time" type="number" min="0" />
            <span class="prefix-input">seconds</span>
          </div>
        </div>
      </div>
    </div>
    <div class="md-layout-item md-size-100">
      <div class="md-layout md-gutter">
        <div class="md-layout-item md-size-50 md-xsmall-size-100">
          <md-checkbox v-model="currentEvent.only_home"></md-checkbox>&nbsp;
          <label>Only at homepage</label>
        </div>
        <div class="md-layout-item md-size-50 md-xsmall-size-100">
          <md-field>
            <label>Show frame on</label>
            <md-select v-model="currentEvent.frame_position">
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
              v-model="currentEvent.start_date"
              :config="config"
              class="form-control"
              placeholder="Select a date"
            ></flat-pickr>
          </div>
        </div>
        <div class="md-layout-item md-size-50 md-xsmall-size-100">
          <label>End date</label>
          <div>
            <flat-pickr
              v-model="currentEvent.end_date"
              :config="config"
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
          v-for="icon of chooseIcons"
          :key="icon.id"
        >
          <md-checkbox v-model="currentEvent.choose_icons" :value="icon.id">
            <i :class="icon.class" aria-hidden="true"></i>
            <br />
            <small>{{ icon.id }}</small>
          </md-checkbox>
        </div>
      </div>
    </div>
    <div class="md-layout-item md-size-100">
      <h4>Choose images</h4>
      <div class="md-layout md-gutter chooseImages">
        <div
          class="md-layout-item md-size-10 md-xsmall-size-20"
          v-for="image of chooseImages"
          :key="image.id"
        >
          <md-checkbox v-model="currentEvent.choose_images" :value="image.id">
            <img :src="image.image" alt />
            <br />
            <small>{{ image.id }}</small>
          </md-checkbox>
        </div>
        <div
          v-if="shopName == 'booktake.myshopify.com'"
          class="md-layout-item md-size-10 md-xsmall-size-20"
          v-for="image of booktakeImages"
          :key="image.id"
        >
          <md-checkbox v-model="currentEvent.choose_images" :value="image.id">
            <img :src="image.image" alt />
            <br />
            <small>{{ image.id }}</small>
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
          <md-checkbox v-model="currentEvent.custom_images" :value="item.url">
            <img :src="item.url" alt />
            <br />
            <small>{{ item.name }}</small>
          </md-checkbox>
        </div>
      </div>
    </div>
    <div class="md-layout-item md-size-100">
      <h4>Choose frames</h4>
      <a class="undo" v-on:click="currentEvent.frames = ''">Clear frame's selection</a>
      <div class="md-layout md-gutter chooseFrames">
        <div
          class="md-layout-item md-size-20 md-small-size-25 md-xsmall-size-50"
          v-for="frame of chooseFrames"
          :key="frame.id"
        >
          <md-radio v-model="currentEvent.frames" :value="frame.id">
            <img :src="frame.image" alt />
            <br />
          </md-radio>
        </div>
      </div>
    </div>
    <div class="md-layout-item md-size-100">
      <md-button class="md-raised md-primary" id="edit-event-button" v-on:click="saveEvent()">Save</md-button>
      <md-button class="md-primary" @click="closeDialog">Close</md-button>
    </div>
  </div>
</template>
 
<script>
const shop = window.shop;
const rootLink = window.rootLink;

module.exports = {
  props: [
    "chooseIcons",
    "chooseImages",
    "chooseFrames",
    "booktakeImages",
    "currentEvent",
    "customImages"
  ],
  data: function() {
    return {
      config: {
        enableTime: true,
        dateFormat: "m/d/Y H:i"
      },
      isEditing: false,
      shopName: shop
    };
  },
  mounted: function() {},
  methods: {
    saveEvent: function() {
      var self = this;
      self.isEditing = true;
      self.$http
        .post(
          "services.php",
          {
            action: "saveEvent",
            shop: shop,
            event: self.currentEvent
          },
          {
            emulateJSON: true
          }
        )
        .then(() => {
          self.isEditing = false;
          ShopifyApp.flashNotice(
            `Edit event : ${self.currentEvent.event_name} successfully !`
          );
          self.$emit("reload-events");
        });
    },
    enableEditEventButton: function(button) {
      if (button.value != "") {
        $("#edit-event-button").removeAttr("disabled");
      } else {
        $("#edit-event-button").prop("disabled", true);
      }
    },
    closeDialog: function() {
      var self = this;
      self.$emit("close-dialog");
    }
  },
  components: {
    "flat-pickr": VueFlatpickr
  }
};
</script>