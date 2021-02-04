<template>
    <div class="md-layout md-gutter">
        <div class="md-layout-item md-size-100">
            <label style="font-weight:600;">Upload your new images</label>
            <p>(Maximum {{ maxImageWidth }}x{{ maxImageHeight }} image dimension)</p>
            <vue-dropzone ref="myVueDropzone" id="upload" :options="config" @vdropzone-removed-file="removeFile" @vdropzone-thumbnail="afterComplete"></vue-dropzone>
            <md-button class="md-raised md-primary upload-image-button" @click="uploadCustomImages">Upload</md-button>
        </div>
        <div class="md-layout-item md-size-100 gallery-wrap">
            <div class="md-layout md-gutter">
                <div class="md-layout-item md-size-10 md-small-size-20 md-xsmall-size-25 gallery-image" v-for="item of customImages" :key="item.id">
                    <img :src="item.url">
                    <div class="image-action">
                        <a class="delete-btn" @click="deleteCustomImage(item)">Delete</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="md-layout-item md-size-100 text-right">
            <md-button class="md-primary" @click="closeDialog">Close</md-button>
        </div>
    </div>
</template>
<script>
const shop = window.shop
const rootLink = window.rootLink

module.exports = {
    props: ['customImages'],
    data: function() {
        return {
            config: {
                url: rootLink+"/services.php",
                maxFilesize: 1, // MB
                // maxFiles: 4,
                thumbnailWidth: 130,
                thumbnailHeight: 130,
                addRemoveLinks: true,
                acceptedFiles: ".png",
                uploadMultiple: false,
            },
            uploadImages: [],
            maxImageWidth: 130,
            maxImageHeight: 130
        };
    },
    mounted: function() {
        
    },
    methods: {
        uploadCustomImages: function () {
            var self = this;
            $(".upload-image-button .md-button-content").text("Uploading...");
            $(".upload-image-button").prop("disabled", true);
            var fd = new FormData();
            fd.append('image',self.uploadImages);
            fd.append('newShopImage',shop);
            axios.post('services.php',fd)
                .then(function (res) {
                    if(res["data"] == true) {
                        $(".upload-image-button .md-button-content").text("Upload");
                        self.$refs.myVueDropzone.removeAllFiles();
                        self.uploadImages = null;
                        ShopifyApp.flashNotice("Upload images succesful !");
                        self.$emit('reload-images');
                    } else {
                        ShopifyApp.flashError('Upload image error !')
                    }
                })
                .catch(function (error) {
                    
                });
        },
        removeFile: function (file) {
            var self = this;
            self.uploadImages = null
            self.enableUploadImageButton();
        },
        afterComplete: function (file,dataUrl) {
            var self = this;
            if(file.width > self.maxImageWidth || file.height > self.maxImageHeight || file.status == "error") {
                ShopifyApp.flashError('Invalid dimension or too large !')
                self.uploadImages = null
                self.enableUploadImageButton();
                // self.$refs.myVueDropzone.removeFile(file);
            } else {
                self.uploadImages = file
                self.enableUploadImageButton();
            }
        },
        deleteCustomImage: function(image) {
            var self = this;
            ShopifyApp.Modal.confirm({
                title: "Delete image?",
                message: "Are you sure you want to delete this image? This action cannot be undone.",
                okButton: "Agree",
                cancelButton: "Disagree",
                style: "danger"
            }, function(result){
                if(result){
                    self.$http
                        .post('services.php', {
                            action  : 'deleteCustomImage',
                            shop    : shop,
                            image   : image,
                        }, {
                            emulateJSON : true
                        })
                        .then(() => {
                            ShopifyApp.flashNotice(`Delete image successful !!!`);
                            var idx = self.customImages.indexOf(image);
                            self.customImages.splice(idx, 1);
                        })
                }
            });
        },
        enableUploadImageButton: function() {
            var self = this;
            if(self.uploadImages != null){
                $(".upload-image-button").removeAttr('disabled');
            } else {
                $(".upload-image-button").prop("disabled", true);
            }
        },
        closeDialog: function() {
            var self = this;
            self.$emit('close-dialog')
        },
    },
    components : {
        vueDropzone: vue2Dropzone
    }   
};
</script>
<style>
    
</style>