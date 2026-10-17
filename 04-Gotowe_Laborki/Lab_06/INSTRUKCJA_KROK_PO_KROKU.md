# ІНСТРУКЦІЯ ДО ЛАБОРАТОРНОЇ РОБОТИ №6 (Час виконання на парі: 3-5 хв)

У каталозі `Lab_06` повністю реалізовано **усі 9 варіантів тем**, передбачених інструкцією викладача Dr inż. Jarosław Wikarek! Усі варіанти побудовані в суворій реляційній моделі (**3NF**) з повноцінною DTD-валідацією через атрибути типу `ID` та `IDREF`.

---

### Таблиця варіантів:
- **Wariant A:** Klient – kupuje – towar (`Wariant_A_Klient_Towar`)
- **Wariant B:** Student – otrzymuje ocenę – przedmiot (`Wariant_B_Student_Ocena`)
- **Wariant C:** Czytelnik – wypożycza – książka (`Wariant_C_Czytelnik_Ksiazka`)
- **Wariant D:** Widz – rezerwuje bilet – spektakl (`Wariant_D_Spektakl_Widz`)
- **Wariant E:** Pacjent – rejestruje wizytę – lekarz (`Wariant_E_Pacjent_Lekarz`)
- **Wariant F:** Uczestnik – zapisuje się – wycieczka (`Wariant_F_Wycieczka_Uczestnik`)
- **Wariant G:** Wykładowca – prowadzi – zajęcia (`Wariant_G_Wykladowca_Zajecia`)
- **Wariant H:** Pracownik – zajmuje – stanowisko (`Wariant_H_Pracownik_Stanowisko`)
- **Wariant I:** Pasażer – rezerwuje lot – samolot (`Wariant_I_Samolot_Pasazer`)

---

### Що було вдосконалено за результатами аудиту:
1. **Реляційний XSLT JOIN (`relacja.xsl`):** Використано конструкцію `<xsl:key>` та функцію `key()`, що дозволяє при відображенні зв'язку автоматично підтягувати повні назви та характеристики батьківських сутностей (наприклад, ім'я клієнта та найменування товару), а не просто сухі ID.
2. **Абсолютні XPath-запити (`index.php`):** Усі запити виправлено на абсолютні шляхи (наприклад, `/baza/elementy_a/element_a`), що гарантує 100% стабільність SimpleXML незалежно від контексту кореневого вузла.
3. **Строга валідація вхідного параметра:** Перед виконанням XPath-запиту переданий `ref1_id` перевіряється за білим списком існуючих ID для захисту від збоїв та ін'єкцій.
4. **Чисті бізнес-дані:** У `baza.xml` усунуто штучні суфікси, виправлено реалістичні дані для всіх сутностей (номери рейсів, назви книг, дати консультацій, місця в залі).

---

### Покроковий алгоритм дій на парі:

1. **Отримання варіанту від викладача:**
   - Викладач призначає літеру варіанту (наприклад, «Wariant B: Student – ocena – przedmiot» або «Wariant H: Pracownik – stanowisko»).

2. **Копіювання файлів:**
   - Зайдіть у папку відповідного варіанту (наприклад, `Wariant_B_Student_Ocena`).
   - Скопіюйте всі файли (`baza.xml`, `tabela.xsl`, `relacja.xsl`, `index.php`, `style.css`) у ваш каталог:
     `D:\Studenci\Cw_06\3ID11B_01\`

3. **Демонстрація викладачу:**
   - **Крок А — Пряме відкриття XML у браузері:**
     Відкрийте `http://st.pl/Cw_06/3ID11B_01/baza.xml` (або напряму в Google Chrome / Firefox). Браузер застосує `<?xml-stylesheet type="text/xsl" href="relacja.xsl"?>` і відобразить красиву зведену таблицю зв'язків із повними назвами сутностей (реляційний JOIN через `key()`).
   - **Крок Б — Демонстрація табличного XSLT:**
     Покажіть викладачу `tabela.xsl`, яка відображає обидва довідники сутностей у вигляді структурованих таблиць.
   - **Крок В — Інтерактивний веб-додаток PHP (SimpleXML / XPath):**
     Відкрийте `http://st.pl/Cw_06/3ID11B_01/index.php`. Виберіть зі списку будь-яку сутність (наприклад, студента чи викладача) та натисніть кнопку **«Filtruj powiązania (SimpleXML + XPath)»**. Система миттєво відфільтрує зв'язані сутності через XPath-вираз `//relacje/relacja[@ref1_id='...']`.
   - **Крок Г — Перевірка валідності DTD:**
     Якщо викладач вимагає перевірки DTD:
     Відкрийте онлайн-валідатор `https://www.xmlvalidation.com/`, завантажте або вставте вміст `baza.xml` разом з внутрішнім DTD. Результат: **«No errors were found»** (100% валідно за специфікацією W3C).

4. **Фіналізація та архівування:**
   - Запакуйте каталог `3ID11B_01` в архів:
     `pti_06_3ID11B_01.zip` (або `pai_06_3ID11B_97882_97889.zip`).
   - Звіт: `Sprawozdanie_Lab_06.docx`.
