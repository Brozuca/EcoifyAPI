# Ecoify API
Серверная часть мобильного приложения Ecoify
Функционал API
- Верификация подключения по ключу, который расположен в заголовке http запроса
- Получение информации от приложения и дальнейшее добавление ее в базу данных 
- При получении запроса от клиентской части: извлечение данных из бд, и отправка их в ответе к запросу
- При получении изображения в кодировке Base64, осуществляется декодирование изображения, сжатие размера и сохранение (исходного размера и уменьшенного) изображения в памяти сервера
- Подсчет примерного расстояния между двумя координатами с помощью сферической геометрии. 

Ecoify mobile app backend
API functionality
- Verification of the connection by the key, which is located in the header of the http request
- Obtaining information from the application and further adding it to the database
- When receiving a request from the client side: extracting data from the database, and sending it in response to the request
- When receiving an image in Base64 encoding, the image is decoded, the size is compressed and the image is saved (original size and reduced) in the server memory
- Calculating the approximate distance between two coordinates using spherical geometry.
