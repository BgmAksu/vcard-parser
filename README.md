# 📇 vCard Parser (PHP)

A lightweight, object-oriented vCard (VCF) parser written in pure PHP, supporting **vCard 4.0 (RFC 6350)**.

This project was developed as part of a technical assignment and focuses on clean code, extensibility, and modern PHP best practices.

---

## 🚀 Features

- Parse **single and multiple vCards** from a file  
- Support for **vCard 4.0 (RFC 6350 subset)**  
- **Object-Oriented design** (Parser, Model, Exporter separation)  
- **No third-party libraries** (pure PHP implementation)  
- Handles **line folding** (multi-line values)  

### Parsing capabilities

- Property name  
- Parameters (including multi-value parameters)  
- Property value  

### Additional features

- Supports **multiple properties with the same name** (e.g., multiple EMAIL fields)  

### Validation

- Ensures `VERSION` exists  
- Ensures `VERSION = 4.0`  
- Detects malformed vCard structures  

- **Error reporting per card**  
- Export parsed vCards to **jCard (JSON format)**  

---

## 🏗️ Project Structure

```text
.
├── src/
│   ├── VCard.php
│   ├── VCardField.php
│   ├── VCardParser.php
│   └── JCardExporter.php
├── vcard_samples/
│   ├── sample.vcf
│   ├── sample_fatal_nested.vcf
│   ├── sample_fatal_unclosed.vcf
│   ├── sample_no_blocks.vcf
│   └── sample_empty.vcf
├── test.php
└── README.md
```

---

## ⚙️ Requirements

- PHP **7.4+**
- No external dependencies

---

## ▶️ Usage

Run the interactive CLI test script:

```bash
php test.php
```

You will see a list of available sample files:

```text
[1] sample.vcf
[2] sample_fatal_nested.vcf
[3] sample_fatal_unclosed.vcf
...
```

### Input options

- Enter a number → run that test case

- Enter ``q`` → exit

---

## 🧪 Example Output

```text
Valid vCards   : 5
Invalid vCards : 5

Parsed vCards
--------------------------------------------------
Card #1
  FN    : Mr John Doe
  EMAIL : jobs@copernica.com
  ORG   : Copernica BV
  TITLE : Software Engineer
...
```

### Error Reporting

```text
Errors
--------------------------------------------------
- Card 3: Each VCARD must contain exactly one VERSION field.
- Card 4: Unsupported VCARD version: 3.0. Only vCard 4.0 is supported.
- Card 5: A VCARD must not contain multiple VERSION fields.
```

---

## 🔄 jCard Export

Example output:

```text
[
  "vcard",
  [
    ["fn", {}, "text", "Mr John Doe"],
    ["email", {"type": ["work"]}, "text", "jobs@copernica.com"]
  ]
]
```

---

## 🧠 Design Decisions

### Object-Oriented Architecture

The project separates responsibilities into dedicated classes:

- ``VCardParser`` → parsing and validation logic
- ``VCard`` → domain model
- ``VCardField`` → field representation
- ``JCardExporter`` → export layer

### Error Handling Strategy

Two parsing modes are supported:

#### Strict mode

`parseFile()` → throws an exception on the first error

#### Report mode

`parseFileWithReport()` → returns:

- valid cards
- list of errors per card

#### Flexible Parsing

- Unknown properties are not rejected
- Custom fields ``(X-*)`` are supported
- Parser is generic and extensible

---

## ⚠️ Limitations

This implementation covers a subset of **RFC 6350**.

Not implemented:

- Full data type handling (date, URI, etc.)
- Escaped character parsing
- Advanced parameter encoding
- Full strict validation of all property formats

---

## 💡 Possible Improvements

- Add custom exception classes
- Add unit tests (PHPUnit)
- Add Composer autoloading
- Extend jCard export with proper value types
- Improve RFC compliance

---

## 🧑‍💻 Author

Begum AKSU YILMAZ

---

## 📄 License

This project is licensed under the Apache License 2.0.
