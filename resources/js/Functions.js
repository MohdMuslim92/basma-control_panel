  // Function to format the Last Pay date as YYYY-MM-DD
  export const formatDate = (dateString) => {
    if (!dateString) return 'User has never paid before'; // Display message if date is null or undefined
    const date = new Date(dateString);
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
  };