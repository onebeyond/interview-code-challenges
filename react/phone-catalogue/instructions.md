# Phone Catalogue
Your task is to write a very simple product catalogue app.

1. Write a REST API using node.js that as a minimum...
 - Has 1 endpoint `/phones`
 - Returns the attached "phones.json" payload
2. Write a React app that displays the phones from the API
- Display an initial list with all phones
- When a phone model is selected from the list, it will render a phone detail view displaying a few more details about that phone
- Display a spinner or placeholder component while the API request is ongoing
- Make it look decent. No need for super sophisticated design, but at a minimum, make it somewhat responsive so that it doesn’t look terrible on a mobile phone. Add images for each device.
3. Push the code to a private github repo with a README.md that explains how to run API & Frontend app 

## Bonus points
1. Add functionality to the REST API to allow for filtering/sorting/paging of phones list and reflect this on front end
2. Dockerize the app.
3. Write realistic unit/end-to-end tests.
