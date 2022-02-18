SELECT CONCAT(users.first_name, ' ', users.last_name) as name,
       author,
       GROUP_CONCAT(books.name SEPARATOR ', ')        as books

FROM test.users
         INNER JOIN user_books on users.id = user_books.user_id
         INNER JOIN books on user_books.book_id = books.id
GROUP BY users.id
HAVING COUNT(books.id) = 2
   AND SUBSTRING_INDEX(GROUP_CONCAT(books.author), ',', 1) = SUBSTRING_INDEX(GROUP_CONCAT(books.author), ',', -1)
