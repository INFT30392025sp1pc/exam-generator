// Get DOM elements
const dropArea = document.getElementById('drop-area');
const fileInput = document.getElementById('file-input');
const preview = document.getElementById('preview');

// Store selected files
let files = [];

// Prevent default drag behaviors for these events
// else different browsers have different default
// behaviours towards these actions
['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
    dropArea.addEventListener(eventName, preventDefaults, false);
    document.body.addEventListener(eventName, preventDefaults, false);
});

// Highlight drop area on drag enter/over
['dragenter', 'dragover'].forEach(eventName => {
    dropArea.addEventListener(eventName, highlight, false);
});

// Remove highlight on drag leave/drop
['dragleave', 'drop'].forEach(eventName => {
    dropArea.addEventListener(eventName, unhighlight, false);
});

// Handle actual drop
dropArea.addEventListener('drop', handleDrop, false);

// Handle file selection via input
fileInput.addEventListener('change', handleFiles);

/**
 * Prevent default drag behaviors and stop event propagation
 * @param {Event} e - The drag/drop event
 */
function preventDefaults(e) {
    e.preventDefault();
    e.stopPropagation();
}

/**
 * Add visual highlight to drop area
 */
function highlight() {
    dropArea.classList.add('highlight');
}

/**
 * Remove visual highlight from drop area
 */
function unhighlight() {
    dropArea.classList.remove('highlight');
}

/**
 * Process files dropped onto the drop area
 * @param {DragEvent} e - The drop event containing file data
 */
function handleDrop(e) {
    // Get files from dataTransfer object
    const dt = e.dataTransfer;
    files = dt.files;

    // Process files (same as file input handler)
    handleFiles({ target: { files } });
}

/**
 * Process selected files and generate previews
 * @param {Event} e - Event containing file list
 */
function handleFiles(e) {
    // Get files from event or use existing files
    files = e.target.files || files;

    // Clear previous previews
    preview.innerHTML = '';

    if (!files.length) return;

    // Process each file
    Array.from(files).forEach((file, index) => {
        if (file.type.match('image.*')) {
            // Create preview for image files
            previewImage(file, index);
        } else {
            // Create preview for non-image files
            previewFile(file, index);
        }
    });

    // Add remove functionality to preview items
    addRemoveListeners();
}

/**
 * Create image preview thumbnail
 * @param {File} file - Image file
 * @param {number} index - File index in array
 */
function previewImage(file, index) {
    const reader = new FileReader();

    // When file is loaded, create preview element
    reader.onload = function(e) {

        // dynamically add image item
        const previewItem = document.createElement('div');
        previewItem.className = 'preview-item';
        previewItem.innerHTML = `
      <img src="${e.target.result}" alt="${file.name}">
      <p>${file.name} (${formatFileSize(file.size)})</p>
      <span class="remove-btn" data-index="${index}">×</span>
    `;
        preview.appendChild(previewItem);
    };

    // Read file as data URL (base64)
    reader.readAsDataURL(file);
}

/**
 * Create preview for non-image files
 * @param {File} file - Any file type
 * @param {number} index - File index in array
 */
function previewFile(file, index) {
    const previewItem = document.createElement('div');
    previewItem.className = 'preview-item';
    previewItem.innerHTML = `
    <p>${file.name} (${formatFileSize(file.size)})</p>
    <span class="remove-btn" data-index="${index}">×</span>
  `;
    preview.appendChild(previewItem);
}

/**
 * Format file size in human-readable format
 * @param {number} bytes - File size in bytes
 * @returns {string} Formatted size
 */
function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}


/**
 * Add click listeners to remove buttons
 */
function addRemoveListeners() {
    document.querySelectorAll('.remove-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            // Get index from data attribute
            const index = parseInt(this.getAttribute('data-index'));

            // Remove file from array
            files = Array.from(files).filter((_, i) => i !== index);

            // Re-render previews
            handleFiles({ target: { files } });
        });
    });
}

document.querySelector('.upload-form').addEventListener('submit', function(e) {
    e.preventDefault();

    if (!files.length) {
        alert('Please select files to upload');
        return;
    }

    // Create FormData object for AJAX upload
    const formData = new FormData();

    // Append all files to FormData
    Array.from(files).forEach((file, index) => {
        formData.append(`files[${index}]`, file);
    });


    // Upload using Fetch API
    uploadFiles(formData);
});

/**
 * Send files to server using Fetch API
 * @param {FormData} formData - Files and additional data
 */
function uploadFiles(formData) {
    fetch('/upload-endpoint', {
        method: 'POST',
        body: formData,
        // Don't set Content-Type header - let browser set it with boundary
    })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            console.log('Upload successful:', data);
            alert(`${files.length} files uploaded successfully!`);

            // Reset UI
            preview.innerHTML = '';
            files = [];
            fileInput.value = '';
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error uploading files: ' + error.message);
        });

}