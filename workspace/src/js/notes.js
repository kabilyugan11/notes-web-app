// This file manages note-related functionalities such as creating, reading, updating, and deleting notes.

document.addEventListener('DOMContentLoaded', function() {
    const notesContainer = document.getElementById('notes-container');
    const createNoteForm = document.getElementById('create-note-form');
    const noteTitleInput = document.getElementById('note-title');
    const noteContentInput = document.getElementById('note-content');

    // Fetch and display notes
    function fetchNotes() {
        fetch('/api/notes/read.php')
            .then(response => response.json())
            .then(data => {
                notesContainer.innerHTML = '';
                data.notes.forEach(note => {
                    const noteElement = document.createElement('div');
                    noteElement.classList.add('note');
                    noteElement.innerHTML = `
                        <h3>${note.title}</h3>
                        <p>${note.content}</p>
                        <button onclick="deleteNote('${note.id}')">Delete</button>
                        <button onclick="editNote('${note.id}', '${note.title}', '${note.content}')">Edit</button>
                    `;
                    notesContainer.appendChild(noteElement);
                });
            })
            .catch(error => console.error('Error fetching notes:', error));
    }

    // Create a new note
    createNoteForm.addEventListener('submit', function(event) {
        event.preventDefault();
        const title = noteTitleInput.value;
        const content = noteContentInput.value;

        fetch('/api/notes/create.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ title, content })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                fetchNotes();
                noteTitleInput.value = '';
                noteContentInput.value = '';
            } else {
                alert('Error creating note: ' + data.message);
            }
        })
        .catch(error => console.error('Error creating note:', error));
    });

    // Delete a note
    window.deleteNote = function(noteId) {
        fetch(`/api/notes/delete.php?id=${noteId}`, {
            method: 'DELETE'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                fetchNotes();
            } else {
                alert('Error deleting note: ' + data.message);
            }
        })
        .catch(error => console.error('Error deleting note:', error));
    };

    // Edit a note
    window.editNote = function(noteId, title, content) {
        noteTitleInput.value = title;
        noteContentInput.value = content;

        createNoteForm.onsubmit = function(event) {
            event.preventDefault();
            fetch(`/api/notes/update.php?id=${noteId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ title: noteTitleInput.value, content: noteContentInput.value })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    fetchNotes();
                    noteTitleInput.value = '';
                    noteContentInput.value = '';
                    createNoteForm.onsubmit = createNoteForm.submit; // Reset to original submit function
                } else {
                    alert('Error updating note: ' + data.message);
                }
            })
            .catch(error => console.error('Error updating note:', error));
        };
    };

    // Initial fetch of notes
    fetchNotes();
});