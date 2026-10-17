# ІНСТРУКЦІЯ ДО ЛАБОРАТОРНОЇ РОБОТИ №6 (Час виконання на парі: 5 хв)

У каталозі `Lab_06` повністю реалізовано **усі 9 варіантів тем** у суворій третій нормальній формі (**3NF**) з DTD-контролем через атрибути `ID` та `IDREF`!

### Як діяти на парі:
1. Викладач озвучує ваш варіант (наприклад, «Wariant B: Student – ocena – przedmiot» або «Wariant H: Pracownik – pracuje – stanowisko»).
2. Зайдіть у відповідну папку (наприклад `Wariant_B_Student_Ocena`).
3. Скопіюйте всі 4 файли (`baza.xml`, `tabela.xsl`, `relacja.xsl`, `index.php`, `style.css`) у каталог:
   `D:\Studenci\Cw_06\3ID11B_01\`
4. Відкрийте браузер Google Chrome:
   - Відкрийте файл `baza.xml` безпосередньо: `http://st.pl/Cw_06/3ID11B_01/baza.xml`.
     -> Браузер застосує XSLT і відобразить красиву зведену таблицю реляцій (Join).
   - Відкрийте додаток PHP: `http://st.pl/Cw_06/3ID11B_01/index.php`.
     -> Виберіть елемент у випадаючому списку, натисніть «Filtruj powiązania». Доведіть викладачу роботу SimpleXML/XPath.
5. Відкрийте онлайн-валідатор `https://www.xmlvalidation.com/`, вставте вміст `baza.xml`, натисніть Validate -> результат: **«No errors were found»** (100% відповідність 3NF та DTD).
6. Запакуйте каталог `3ID11B_01` в архів: `pti_06_3ID11B_01.zip` (або `pai_06_3ID11B_97882_97889.zip`).
