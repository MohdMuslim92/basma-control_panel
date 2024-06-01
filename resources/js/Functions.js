  // Function to format the Last Pay date as YYYY-MM-DD
  export const formatDate = (dateString) => {
    if (!dateString) return 'User has never paid before'; // Display message if date is null or undefined
    const date = new Date(dateString);
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
  };

  // Function to display a notification
  export const showNotification = (message) => {
    const notificationContainer = document.getElementById('notification-container');
  
    // Create notification element
    const notification = document.createElement('div');
    notification.className = 'notification';
    notification.textContent = message;
  
    // Apply styles for notification
    notification.style.backgroundColor = 'green'; // Green background
    notification.style.color = 'white'; // White text
    notification.style.fontWeight = 'bold'; // Bold text
    notification.style.padding = '10px'; // Increase padding for a bigger box
    // Append notification to container
    notificationContainer.appendChild(notification);
  
    // Fade out and remove notification after 5 seconds
    setTimeout(() => {
      notification.classList.add('fade-out');
      setTimeout(() => {
        notification.remove();
      }, 500); // Fading animation duration
    }, 5000); // Notification display duration
  };
  