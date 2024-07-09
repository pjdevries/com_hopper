(function () {
    window.PIM = window.PIM || {};

    class UploadBox {
        elUploadBox;
        elFileInput;

        dtDropped = new DataTransfer();

        constructor(elUploadBox) {
            this.elUploadBox = elUploadBox;
            this.elFileInput = elUploadBox.querySelector('input[type="file"]');

            if (isAdvancedUpload()) {
                elUploadBox.classList.add('has-advanced-upload');

                elUploadBox.addEventListener('dragenter', e => this.dragOver(e, this));
                elUploadBox.addEventListener('dragover', e => this.dragOver(e, this));
                elUploadBox.addEventListener('dragleave', e => this.dragLeave(e, this));
                elUploadBox.addEventListener('dragend', e => this.dragLeave(e, this));
                elUploadBox.addEventListener('drop', e => this.drop(e, this));

                if (this.elFileInput) {
                    this.elFileInput.addEventListener('change', e => this.showFiles(e.target.files));
                }
            }
        }

        dragOver(e, uploadBox) {
            uploadBox.dragAny(e, uploadBox);

            uploadBox.elUploadBox.classList.add('is-dragover');
        }

        dragLeave(e, uploadBox) {
            uploadBox.dragAny(e, uploadBox);

            uploadBox.elUploadBox.classList.remove('is-dragover');
        }

        drop(e, uploadBox) {
            uploadBox.dragLeave(e, uploadBox);

            uploadBox.addToDropped(e.dataTransfer, uploadBox);

            uploadBox.showFiles(uploadBox.dtDropped.files);

            [...e.dataTransfer.files].forEach((file, i) => {
                // console.log(`… file[${i}].name = ${file.name}`);
                console.log(file);
            });
            [...e.dataTransfer.items].forEach((item, i) => {
                // console.log(`… file[${i}].name = ${file.name}`);
                console.log(item);

                const file = item.getAsFile();
                console.log(file);
            });
        }

        dragAny(e, uploadBox) {
            e.preventDefault();
            e.stopPropagation();
        }

        addToDropped(dtMore, uploadBox) {
            // const dt = new DataTransfer()

            Array.from(dtMore.files).forEach(file => uploadBox.dtDropped.items.add(file));
        }

        setFilesForSubmission() {
            if (!this.elFileInput) {
                return;
            }

            this.elFileInput.files = this.dtDropped.files;
        }

        showFiles(files) {
            if (!this.elFileInput) {
                return;
            }

            const elLabel = this.elUploadBox.querySelector('label[for="' + this.elFileInput.id + '"]');

            if (!elLabel) {
                return;
            }

            elLabel.innerHTML = (files.length > 1 ? (this.elFileInput.getAttribute('data-multiple-caption') || '').replace('{count}', files.length) : files[0].name);
        }
    }

    const isAdvancedUpload = () => {
        const div = document.createElement('div');

        return (('draggable' in div) || ('ondragstart' in div && 'ondrop' in div)) && 'FormData' in window && 'FileReader' in window;
    };

    const uploadBoxes = [];

    const initUploadBoxes = () => {
        const elUploadBoxes = document.querySelectorAll('.upload-box');

        elUploadBoxes.forEach(elUploadBox => {
            const form = elUploadBox.closest('form');

            if (!form) {
                return;
            }

            const uploadBox = new UploadBox(elUploadBox);

            uploadBoxes.push(uploadBox);

            form.addEventListener('submit', e => {
                if (form.classList.contains('is-uploading') || !uploadBox.dtDropped || !uploadBox.dtDropped.files.length) {
                    return;
                }

                form.classList.add('is-uploading');
                form.classList.remove('is-error');

                uploadBox.setFilesForSubmission();

                form.submit();
            });
        });
    };

    const setEnctype = () => {
        const fields = Joomla.getOptions('obixUploadField')['fields'];

        Object.entries(fields).forEach(([id, name]) => {
            const elInput = document.getElementById(id);

            if (!elInput) {
                return;
            }

            const elForm = elInput.closest('form');

            if (!elForm) {
                return;
            }

            const enctype = elForm.getAttribute('enctype');

            if (!enctype || enctype !== 'multipart/form-data') {
                elForm.setAttribute('enctype', 'multipart/form-data');
            }
        });
    };

    document.addEventListener('DOMContentLoaded', e => {
        // debugger;
        initUploadBoxes();
        setEnctype();
    });
})();