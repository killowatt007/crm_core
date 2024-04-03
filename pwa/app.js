window.addEventListener('load', () => 
{
  if ('serviceWorker' in navigator)
  {
    navigator.serviceWorker.register('/pwa/sw.js')
      .then(registration => {
          console.log('Service worker successfully registered');
      })
      .catch(error => {
          console.log('Service worker registration failed', error);
      });
  }
});