// Hello! If you don't want typescript, just rename this file to index.js.
require('../scss/main.scss')

console.log('hello world!')

window.addEventListener('load', async () => {
    const result = await fetch('/api/message');
    const data = await result.json();

    document.getElementById('api-route-payload').innerText = data.message;

    alert('Hello! If you see this alert, it means that the frontend is working correctly. You can now start building your app!');
});
