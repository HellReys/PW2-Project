// Dashboard JavaScript with AJAX functionality

$(document).ready(function() {
    // Check if user is logged in
    checkAuth();
    
    // Load tasks on page load
    loadTasks();
    
    // Event Listeners
    $('#saveTaskBtn').on('click', addTask);
    $('#updateTaskBtn').on('click', updateTask);
    $('#logoutBtn').on('click', logout);
    $('#searchInput').on('keyup', debounce(searchTasks, 500));
    
    // Set minimum date to today
    const today = new Date().toISOString().split('T')[0];
    $('#taskDueDate, #editTaskDueDate').attr('min', today);
});

// Check Authentication
function checkAuth() {
    $.ajax({
        url: '../../backend/api/check_session.php',
        type: 'GET',
        success: function(response) {
            if(response.status !== 'authenticated') {
                window.location.href = 'login.html';
            }
        },
        error: function() {
            window.location.href = 'login.html';
        }
    });
}

// Load Tasks (READ operation)
function loadTasks() {
    $.ajax({
        url: '../../backend/api/tasks/list.php',
        type: 'GET',
        success: function(data) {
            if(Array.isArray(data)) {
                displayTasks(data);
                updateStats(data);
            } else if(data.status === 'error') {
                showAlert(data.message, 'danger');
            }
        },
        error: function(xhr, status, error) {
            showAlert('Failed to load tasks', 'danger');
            $('#tasksContainer').html('<p class="text-center text-muted">Failed to load tasks</p>');
        }
    });
}

// Display Tasks
function displayTasks(tasks) {
    const container = $('#tasksContainer');
    
    if(tasks.length === 0) {
        container.html(`
            <div class="text-center py-5">
                <i class="bi bi-inbox inbox-icon"></i>
                <p class="text-muted mt-3">No tasks found. Create your first task!</p>
            </div>
        `);
        return;
    }
    
    let html = '';
    tasks.forEach(task => {
        const statusClass = getStatusClass(task.status);
        const statusBadge = getStatusBadge(task.status);
        const categoryBadge = getCategoryBadge(task.category);
        
        html += `
            <div class="task-card card mb-3 ${statusClass}">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h5 class="card-title mb-2">
                                ${escapeHtml(task.title)}
                                ${statusBadge}
                            </h5>
                            <p class="card-text text-muted mb-2">${escapeHtml(task.description)}</p>
                            <div class="mb-2">
                                ${categoryBadge}
                                <span class="badge bg-secondary">
                                    <i class="bi bi-calendar"></i> ${formatDate(task.due_date)}
                                </span>
                            </div>
                        </div>
                        <div class="col-md-4 text-end">
                            <button class="btn btn-sm btn-primary me-1" onclick="editTask(${task.id})">
                                <i class="bi bi-pencil"></i> Edit
                            </button>
                            <button class="btn btn-sm btn-success me-1" onclick="uploadFile(${task.id})">
                                <i class="bi bi-upload"></i> Upload
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="deleteTask(${task.id})">
                                <i class="bi bi-trash"></i> Delete
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
    
    container.html(html);
}

// Update Statistics
function updateStats(tasks) {
    const total = tasks.length;
    const pending = tasks.filter(t => t.status === 'pending').length;
    const completed = tasks.filter(t => t.status === 'completed').length;
    const overdue = tasks.filter(t => t.status === 'overdue').length;
    
    $('#totalTasks').text(total);
    $('#pendingTasks').text(pending);
    $('#inProgressTasks').text(completed);
    $('#completedTasks').text(overdue);
}

// Add Task (CREATE operation)
function addTask() {
    const title = $('#taskTitle').val().trim();
    const description = $('#taskDescription').val().trim();
    const category_id = $('#taskCategory').val();
    const due_date = $('#taskDueDate').val();
    
    // Validation
    if(!title || !description || !category_id || !due_date) {
        showAlert('Please fill in all required fields', 'warning');
        return;
    }
    
    const submitBtn = $('#saveTaskBtn');
    submitBtn.html('<span class="spinner-border spinner-border-sm me-2"></span>Saving...').prop('disabled', true);
    
    // AJAX request to add task
    $.ajax({
        url: '../../backend/api/tasks/add.php',
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({
            title: title,
            description: description,
            category_id: category_id,
            due_date: due_date
        }),
        success: function(response) {
            if(response.status === 'success') {
                showAlert('Task added successfully!', 'success');
                $('#addTaskModal').modal('hide');
                $('#addTaskForm')[0].reset();
                loadTasks();
                
                // Handle file upload if file is selected
                const fileInput = $('#taskFile')[0];
                if(fileInput.files.length > 0) {
                    // Note: We would need the task ID from the response to upload file
                    // This is a simplified version
                    uploadFileForNewTask(fileInput.files[0]);
                }
            } else {
                showAlert(response.message || 'Failed to add task', 'danger');
            }
            submitBtn.html('<i class="bi bi-save"></i> Save Task').prop('disabled', false);
        },
        error: function() {
            showAlert('An error occurred while adding the task', 'danger');
            submitBtn.html('<i class="bi bi-save"></i> Save Task').prop('disabled', false);
        }
    });
}

// Edit Task - Load data into modal
function editTask(taskId) {
    // First, get the task data
    $.ajax({
        url: '../../backend/api/tasks/list.php',
        type: 'GET',
        success: function(data) {
            const task = data.find(t => t.id == taskId);
            if(task) {
                $('#editTaskId').val(task.id);
                $('#editTaskTitle').val(task.title);
                $('#editTaskDescription').val(task.description);
                $('#editTaskCategory').val(task.category_id || 1);
                $('#editTaskDueDate').val(task.due_date);
                $('#editTaskStatus').val(task.status || 'pending');
                $('#editTaskModal').modal('show');
            }
        }
    });
}

// Update Task (UPDATE operation)
function updateTask() {
    const id = $('#editTaskId').val();
    const title = $('#editTaskTitle').val().trim();
    const description = $('#editTaskDescription').val().trim();
    const category_id = $('#editTaskCategory').val();
    const due_date = $('#editTaskDueDate').val();
    const status = $('#editTaskStatus').val();
    
    if(!title || !description || !category_id || !due_date || !status) {
        showAlert('Please fill in all required fields', 'warning');
        return;
    }
    
    const submitBtn = $('#updateTaskBtn');
    submitBtn.html('<span class="spinner-border spinner-border-sm me-2"></span>Updating...').prop('disabled', true);
    
    $.ajax({
        url: '../../backend/api/tasks/update.php',
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({
            id: id,
            title: title,
            description: description,
            category_id: category_id,
            due_date: due_date,
            status: status
        }),
        success: function(response) {
            if(response.status === 'success') {
                showAlert('Task updated successfully!', 'success');
                $('#editTaskModal').modal('hide');
                loadTasks();
            } else {
                showAlert(response.message || 'Failed to update task', 'danger');
            }
            submitBtn.html('<i class="bi bi-save"></i> Update Task').prop('disabled', false);
        },
        error: function() {
            showAlert('An error occurred while updating the task', 'danger');
            submitBtn.html('<i class="bi bi-save"></i> Update Task').prop('disabled', false);
        }
    });
}

// Delete Task (DELETE operation)
function deleteTask(taskId) {
    if(!confirm('Are you sure you want to delete this task?')) {
        return;
    }
    
    $.ajax({
        url: '../../backend/api/tasks/delete.php',
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({ id: taskId }),
        success: function(response) {
            if(response.status === 'success') {
                showAlert('Task deleted successfully!', 'success');
                loadTasks();
            } else {
                showAlert(response.message || 'Failed to delete task', 'danger');
            }
        },
        error: function() {
            showAlert('An error occurred while deleting the task', 'danger');
        }
    });
}

// Search Tasks (Composite search - title and category)
function searchTasks() {
    const keyword = $('#searchInput').val().trim();
    
    if(keyword === '') {
        loadTasks();
        return;
    }
    
    $.ajax({
        url: '../../backend/api/tasks/search.php?q=' + encodeURIComponent(keyword),
        type: 'GET',
        success: function(data) {
            if(Array.isArray(data)) {
                displayTasks(data);
                updateStats(data);
            }
        },
        error: function() {
            showAlert('Search failed', 'danger');
        }
    });
}

// Upload File
function uploadFile(taskId) {
    const input = document.createElement('input');
    input.type = 'file';
    input.onchange = function(e) {
        const file = e.target.files[0];
        if(!file) return;
        
        // Check file size (10MB max)
        if(file.size > 10 * 1024 * 1024) {
            showAlert('File size must be less than 10MB', 'danger');
            return;
        }
        
        const formData = new FormData();
        formData.append('file', file);
        formData.append('task_id', taskId);
        
        $.ajax({
            url: '../../backend/api/tasks/upload.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if(response.status === 'success') {
                    showAlert('File uploaded successfully!', 'success');
                } else {
                    showAlert(response.message || 'Upload failed', 'danger');
                }
            },
            error: function() {
                showAlert('An error occurred during upload', 'danger');
            }
        });
    };
    input.click();
}

// Logout
function logout() {
    if(!confirm('Are you sure you want to logout?')) {
        return;
    }
    
    $.ajax({
        url: '../../backend/api/logout.php',
        type: 'POST',
        success: function() {
            showAlert('Logged out successfully', 'success');
            setTimeout(function() {
                window.location.href = 'login.html';
            }, 1000);
        },
        error: function() {
            window.location.href = 'login.html';
        }
    });
}

// Utility Functions
function showAlert(message, type) {
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show alert-floating" role="alert">
            <i class="bi bi-${type === 'success' ? 'check-circle' : type === 'warning' ? 'exclamation-triangle' : 'x-circle'}"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    $('#alertContainer').html(alertHtml);
    
    setTimeout(function() {
        $('.alert-floating').fadeOut();
    }, 5000);
}

function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, m => map[m]);
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
}

function getStatusClass(status) {
    const classes = {
        'pending': 'status-pending',
        'completed': 'status-completed',
        'overdue': 'status-overdue'
    };
    return classes[status] || '';
}

function getStatusBadge(status) {
    const badges = {
        'pending': '<span class="badge bg-warning text-dark">Pending</span>',
        'completed': '<span class="badge bg-success">Completed</span>',
        'overdue': '<span class="badge bg-danger">Overdue</span>'
    };
    return badges[status] || '<span class="badge bg-secondary">Unknown</span>';
}

function getCategoryBadge(category) {
    const colors = {
        'Work': 'primary',
        'Personal': 'success',
        'Shopping': 'warning',
        'Health': 'danger',
        'Education': 'info'
    };
    const color = colors[category] || 'secondary';
    return `<span class="badge bg-${color}"><i class="bi bi-tag"></i> ${category}</span>`;
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

function uploadFileForNewTask(file) {
    // This would require getting the task ID from the add task response
    // Simplified implementation
    showAlert('Please upload files from the task list after creation', 'info');
}