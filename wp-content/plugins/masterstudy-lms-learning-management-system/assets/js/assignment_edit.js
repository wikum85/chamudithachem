"use strict";

(function ($) {
  var content;
  var edit_timeout;
  $(window).load(function () {
    if (typeof tinyMCE !== 'undefined') {
      getEditor();
    }
  });
  $('.btn-accept-assignment').on('click', function (e) {
    e.preventDefault();
    var $draft = $('.stm-lms-course__assignment-draft');
    if ($draft.hasClass('loading')) return false;
    var formData = new FormData();
    formData.append('content', content);
    formData.append('draft_id', stm_lms_draft_id);
    formData.append('course_id', stm_lms_course_id);
    var url = stm_lms_ajaxurl + '?action=stm_lms_accept_draft_assignment&nonce=' + stm_lms_nonces['stm_lms_accept_draft_assignment'];
    $.ajax({
      url: url,
      type: 'POST',
      dataType: 'json',
      context: this,
      processData: false,
      data: formData,
      contentType: false,
      beforeSend: function beforeSend() {
        $draft.addClass('loading');
      },
      complete: function complete() {
        location.reload();
        $draft.removeClass('loading');
      }
    });
  });
  $(document).ready(function () {
    new Vue({
      el: '#stm_lms_assignment_file_loader',
      data: function data() {
        return {
          files: stm_lms_assignment_files,
          translations: stm_lms_assignment_translations
        };
      },
      methods: {
        handleFileUpload: function handleFileUpload() {
          var vm = this;
          Array.prototype.forEach.call(this.$refs['lms_group_csv'].files, function (file) {
            vm.files.push({
              data: {
                'loading': true,
                'status': '',
                'name': file.name
              },
              src: file
            });
          });
          this.$set(this, 'files', this.files);
          vm.uploadFiles();
        },
        uploadFiles: function uploadFiles() {
          var vm = this;
          vm.files.forEach(function (file, key) {
            if (typeof file.data === 'undefined') return false;
            if (file.data['status']) return false;
            vm.uploadFile(file, key);
          });
        },
        uploadFile: function uploadFile(file, key) {
          var vm = this;
          var formData = new FormData();
          formData.append('file', file.src);
          formData.append('draft_id', stm_lms_draft_id);
          vm.$set(file.data, 'status', 'processing');
          var url = stm_lms_ajaxurl + '?action=stm_lms_upload_assignment_file&nonce=' + stm_lms_nonces['stm_lms_upload_file_assignment'];
          vm.$http.post(url, formData, {
            headers: {
              'Content-Type': 'multipart/form-data'
            }
          }).then(function (r) {
            var res = r.body;
            vm.$set(file.data, 'status', 'processed');

            if (res.error) {
              vm.$set(file.data, 'error', res.error);
              vm.$set(file.data, 'message', res.message);
              vm.$set(file.data, 'status', 'failed');
              setTimeout(function () {
                vm.$set(vm.files, key, []);
              }, 3000);
            } else {
              vm.$set(file.data, 'status', 'uploaded');
            }

            if (res.id) {
              vm.$set(file.data, 'id', res.id);
              vm.$set(file.data, 'link', res.link);
            }

            vm.$set(file.data, 'loading', false);
          });
        },
        deleteFile: function deleteFile(file, key) {
          var vm = this;
          if (!confirm(vm.translations["delete"])) return false;
          var formData = new FormData();
          formData.append('file_id', file.data.id);
          var url = stm_lms_ajaxurl + '?action=stm_lms_delete_assignment_file&nonce=' + stm_lms_nonces['stm_lms_delete_assignment_file'];
          vm.$set(vm.files, key, []);
          vm.$http.post(url, formData);
        }
      },
      watch: {}
    });
  });

  function getEditor() {
    console.log(stm_lms_editor_id);
    var editor = tinyMCE.get(stm_lms_editor_id);
    console.log(editor);

    if (editor === null) {
      setTimeout(function () {
        getEditor();
      }, 1000);
    } else {
      content = editor.getContent();
      editor.on('keyup', function (e) {
        content = editor.getContent();
      });
    }
  }
})(jQuery);