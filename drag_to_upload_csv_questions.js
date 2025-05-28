// Get DOM elements
const dropArea = document.getElementById('drop-area');
const fileInput = document.querySelector('input[type="file"]');
const preview = document.getElementById('preview');
const uploadBtn = document.getElementById('upload-btn');
const nextBtn = document.querySelector('a[href="create_exam_step3.php"]');

// Store selected file
let file = null;

// Prevent default drag behaviors
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
fileInput.addEventListener('change', handleFileSelect);

// Handle upload button click
uploadBtn.addEventListener('click', handleUpload);

/**
 * Prevent default drag behaviors and stop event propagation
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
 * Process file dropped onto the drop area
 */
function handleDrop(e) {
    const dt = e.dataTransfer;
    const files = dt.files;

    if (files.length) {
        handleFileSelect({ target: { files } });
    }
}

/**
 * Process selected file and generate preview
 */
function handleFileSelect(e) {
    // Reset previous file
    file = null;
    preview.innerHTML = '';

    const files = e.target.files;
    if (!files.length) return;

    // Only accept the first file (CSV)
    const selectedFile = files[0];

    // Validate file type (CSV)
    if (!selectedFile.name.endsWith('.csv')) {
        preview.innerHTML = '<div class="alert alert-danger">Only CSV files are allowed</div>';
        return;
    }

    file = selectedFile;
    previewFile(file);
}

/**
 * Create preview for the CSV file
 */
function previewFile(file) {
    const previewItem = document.createElement('div');
    previewItem.className = 'preview-item alert alert-success';
    previewItem.innerHTML = `
        <p>${file.name} (${formatFileSize(file.size)})</p>
        <span class="remove-btn">×</span>
    `;
    preview.appendChild(previewItem);

    // Add remove functionality
    previewItem.querySelector('.remove-btn').addEventListener('click', () => {
        file = null;
        fileInput.value = '';
        preview.innerHTML = '';
    });
}

/**
 * Format file size in human-readable format
 */
function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

/**
 * Handle file upload
 */
function handleUpload() {
    if (!file) {
        showAlert('Please select a CSV file to upload', 'danger');
        return;
    }

    const formData = new FormData();
    formData.append('file', file);
    formData.append('upload', '1');

    // Disable buttons during upload
    uploadBtn.disabled = true;
    nextBtn.classList.add('disabled');
    const originalText = uploadBtn.textContent;
    uploadBtn.textContent = 'Uploading...';

    fetch('upload_question_file.php', {
        method: 'POST',
        body: formData
    })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.text();
        })
        .then(text => {
            // Check if response contains success message
            if (text.includes('successfully')) {
                showAlert('Questions uploaded successfully!', 'success');
                // Reset file selection
                file = null;
                fileInput.value = '';
                preview.innerHTML = '';
                // Reload to show any PHP session messages
                setTimeout(() => location.reload(), 1500);
            } else {
                throw new Error(text || 'Unknown error occurred');
            }
        })
        .catch(error => {
            showAlert('Error: ' + error.message, 'danger');
            console.error('Upload error:', error);
        })
        .finally(() => {
            uploadBtn.disabled = false;
            nextBtn.classList.remove('disabled');
            uploadBtn.textContent = originalText;
        });
}

/**
 * Show alert message
 */
function showAlert(message, type) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} mt-3`;
    alertDiv.textContent = message;

    // Remove any existing alerts
    const existingAlert = preview.querySelector('.alert');
    if (existingAlert) {
        preview.removeChild(existingAlert);
    }

    preview.appendChild(alertDiv);

    // Auto-remove after 5 seconds
    setTimeout(() => {
        if (alertDiv.parentNode === preview) {
            preview.removeChild(alertDiv);
        }
    }, 5000);
}