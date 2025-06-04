// Get DOM elements
const dropArea = document.getElementById('drop-area');
const fileInput = document.querySelector('.upload-form input[type="file"]');
const preview = document.getElementById('preview');
const uploadForm = document.querySelector('.upload-form');
const trussNameInput = document.querySelector('.upload-form input[name="truss_name"]');

// Store selected files
let files = [];

// Prevent default drag behaviors for these events
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

    // Only process the first file (since the PHP only handles one file)
    const file = files[0];

    if (file.type.match('image.*')) {
        // Create preview for image files
        previewImage(file, 0);
    } else {
        // Show error for non-image files
        preview.innerHTML = '<div class="alert alert-danger">Only image files are allowed</div>';
        files = [];
        return;
    }

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
        const previewItem = document.createElement('div');
        previewItem.className = 'preview-item';
        previewItem.innerHTML = `
            <img src="${e.target.result}" alt="${file.name}" class="img-thumbnail mb-2">
            <p>${file.name} (${formatFileSize(file.size)})</p>
            <span class="remove-btn" data-index="${index}">×</span>
        `;
        preview.appendChild(previewItem);
    };

    // Read file as data URL (base64)
    reader.readAsDataURL(file);
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
            // Remove file from array
            files = [];

            // Re-render previews
            handleFiles({ target: { files } });
        });
    });
}

// Handle form submission
uploadForm.addEventListener('submit', function(e) {
    e.preventDefault();

    if (!files.length) {
        alert('Please select a file to upload');
        return;
    }

    if (!trussNameInput.value.trim()) {
        alert('Please enter a truss name');
        return;
    }

    // Create FormData object for AJAX upload
    const formData = new FormData();

    // Append the single file (PHP expects 'truss_image')
    formData.append('truss_image', files[0]);

    // Append the truss name
    formData.append('truss_name', trussNameInput.value.trim());

    // Add the upload flag
    formData.append('upload', '1');

    // Upload using Fetch API
    uploadFiles(formData);
});

/**
 * Send files to server using Fetch API
 * @param {FormData} formData - Files and additional data
 */
function uploadFiles(formData) {
    // Show loading state
    const uploadBtn = document.getElementById('upload-btn');
    const originalText = uploadBtn.textContent;
    uploadBtn.disabled = true;
    uploadBtn.textContent = 'Uploading...';

    fetch('upload_truss.php', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest' // Identify as AJAX request
        }
    })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Instead of showing alert, reload to show PHP session message
                window.location.reload();
            } else {
                throw new Error(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            // Show temporary error message (will be replaced by PHP message on reload)
            const statusDiv = document.getElementById('status');
            statusDiv.innerHTML = `<div class="alert alert-danger">${error.message}</div>`;
            setTimeout(() => {
                window.location.reload(); // Still reload to sync state
            }, 3000);
        })
        .finally(() => {
            uploadBtn.disabled = false;
            uploadBtn.textContent = originalText;
        });
}