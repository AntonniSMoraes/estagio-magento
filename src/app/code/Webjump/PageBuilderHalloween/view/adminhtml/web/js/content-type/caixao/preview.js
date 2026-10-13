define([
    'Magento_PageBuilder/js/content-type/preview',
    'Magento_PageBuilder/js/uploader'
], function (BasePreview, Uploader) {
    'use strict';

    function Preview() {
        BasePreview.apply(this, arguments);
    }

    Preview.prototype = Object.create(BasePreview.prototype);
    Preview.prototype.constructor = Preview;
    Preview.prototype.getUploader = function () {
        if (!this.uploader) {
            this.uploader = new Uploader(
                'caixao_image_' + this.contentType.id,
                this.config.additional_data.uploaderConfig,
                this.contentType.id,
                this.contentType.dataStore,
                this.contentType.dataStore.get('image', '')
            );
        }
        return this.uploader;
    };

    return Preview;
});
