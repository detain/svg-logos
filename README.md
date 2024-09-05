Here's a sample `README.md` file that would be appropriate for your `detain/svg-logos` project:

---

# SVG Logos Collection

This project is a collection of SVG logos stored in the `svg/` directory, providing easy access to high-quality logos. The project includes a script to download and update logos from [WorldVectorLogo](https://worldvectorlogo.com).

## Features
- **Logo Collection**: SVG logos are stored in the `svg/` directory.
- **Automated Downloading**: Run a PHP script to download logos from WorldVectorLogo.
- **Metadata**: Each logo has an associated entry in `svgs.json` with its ID, name, logo URL, and tags.
- **Caching**: HTML files grabbed during logo generation are cached in the `cache/` directory to speed up subsequent runs.

## Usage

### Download Logos

To download all logos and store them in the `svg/` directory, run the following command:

```bash
php bin/mirror_worldvectorlogo.php
```

This will automatically download all logos listed in the `svgs.json` file from WorldVectorLogo.

### Example of `svgs.json`

The `svgs.json` file is a JSON array containing metadata about each logo, with entries like:

```json
{
    "apple-11": {
        "id": "apple-11",
        "name": "Apple",
        "logo": "https://cdn.worldvectorlogo.com/logos/apple-11.svg",
        "tags": [
            "Apple"
        ]
    }
}
```

- **id**: Unique identifier for the logo.
- **name**: Human-readable name of the logo.
- **logo**: URL to download the SVG file.
- **tags**: Tags associated with the logo for categorization.

### Directory Structure

```
.
├── bin/
│   └── mirror_worldvectorlogo.php   # PHP script to download logos
├── cache/                           # HTML files cached during generation
├── svg/                             # Directory where logos are stored
├── svgs.json                        # Metadata about each logo
└── README.md                        # Project documentation
```

## Contributing

Contributions to this project are welcome! Please ensure that:
- New logos are added to `svgs.json` with the correct metadata.
- All logos are stored in the `svg/` directory in SVG format.

To contribute, simply fork this repository, make your changes, and open a pull request.

## License

This project is licensed under the [MIT License](LICENSE).

---

This README outlines the project structure, how to run the script, and what the JSON metadata looks like. You can customize this further as needed!
