document.addEventListener('DOMContentLoaded', () => {
    fetch('./php/get_events.php')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            const events = data.events;
            const eventsContainer = document.getElementById('eventsContainer');
            if (data.error) {
                console.error(data.error);
                return;
            }

            events.forEach(event => {
                console.log('Fetched event ID:', event.event_id); // Log event ID

                const eventCard = document.createElement('div');
                eventCard.classList.add('event-card', 'card', 'mb-4', 'p-3');
                
                const deleteButton = event.is_owner ? 
                    `<div class="text-center">
                        <button class="delete-btn btn btn-danger mt-2" data-event-id="${event.event_id}">Delete</button>
                    </div>` 
                    : '';

                eventCard.innerHTML = `
                    <h3>${event.event_name}</h3>
                    <p>Date: ${event.event_date}</p>
                    <p>Time: ${event.event_time}</p>
                    <p>Location: ${event.location}</p>
                    <p>Description: ${event.description}</p>
                    <p>Created by: ${event.username}</p>
                    ${deleteButton} <!-- Conditionally display delete button -->
                `;

                eventsContainer.appendChild(eventCard);
            });

            // Add event listener for delete buttons
            document.querySelectorAll('.delete-btn').forEach(button => {
                button.addEventListener('click', () => {
                    const eventId = button.getAttribute('data-event-id');
                    console.log('Deleting event ID:', eventId); // Log event ID when delete button is clicked

                    fetch('./php/delete_event.php', {
                        method: 'POST', // Use POST for compatibility
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `event_id=${encodeURIComponent(eventId)}`
                    })
                    .then(response => response.text()) // Read response as text
                    .then(text => {
                        try {
                            const result = JSON.parse(text); // Attempt to parse JSON
                            console.log('Server response:', result); // Log server response
                            if (result.success) {
                                alert('Event deleted successfully');
                                button.closest('.event-card').remove();
                            } else {
                                alert('Failed to delete event');
                            }
                        } catch (e) {
                            console.error('Failed to parse JSON response:', e);
                            console.error('Response text:', text);
                        }
                    })
                    .catch(error => console.error('Error deleting event:', error));
                });
            });
        })
        .catch(error => console.error('Error fetching events:', error));
});
