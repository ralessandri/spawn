# HellYeah Spawn TYPO3 CMS Extension

The **HellYeah Spawn Extension** is a TYPO3 extension designed to streamline the
development process by providing a set of CLI commands to generate boilerplate
code for common TYPO3 classes, such as controllers, commands, models,
repositories, middleware, and events. Inspired by Symfony Maker, it ensures
adherence to TYPO3 conventions (e.g., class naming like `AwesomeController`) and
automates repetitive tasks, boosting developer productivity.

## Features

- Generates TYPO3-compliant classes with proper namespaces, file paths, and
  configurations.
- Supports interactive and non-interactive CLI usage.
- Validates inputs (e.g., class names, command names) to ensure TYPO3 standards.
- Extensible architecture for adding new class types.
- Integrates with TYPO3's extension structure and PSR-4 autoloading.

## Installation

To install the HellYeah Spawn Extension using Composer, follow these steps:

1. **Require the Extension**
   Run the following command in your TYPO3 project root:
   ```bash
   composer require hellyeah/spawn
   ```

2. **Activate the Extension**
   Activate the extension in the TYPO3 backend under *Admin Tools > Extensions*
   or via CLI:
   ```bash
   vendor/bin/typo3 extension:activate spawn
   ```

3. **Verify Installation**
   Ensure the extension is listed in your `composer.json` and active in TYPO3.
   The CLI commands will now be available.

## CLI Commands

The extension provides a set of CLI commands to generate TYPO3 classes. All
commands follow the format `vendor/bin/typo3 <command>` and support interactive
prompts or non-interactive arguments for automation.

### Available Commands

1. **`spawn:controller`**
   Creates a new TYPO3 action controller class.
   **Description**: Generates a controller class in `Classes/Controller/` with
   the suffix `Controller`.
   **Arguments**:
    - `extension`: The target extension key (e.g., `my_extension`). Optional;
      prompts interactively if omitted.
    - `controller`: The controller class name (e.g., `AwesomeController`). Must
      end with `Controller`. Optional; prompts interactively if omitted.
    - `namespace`: The namespace prefix (e.g., `Vendor\Extension\`). Optional;
      defaults to extension's PSR-4 prefix or prompts interactively.
      **Example**:
   ```bash
   vendor/bin/typo3 spawn:controller my_extension AwesomeController Vendor\Extension\
   ```

2. **`spawn:command`**
   Creates a new TYPO3 CLI command class.
   **Description**: Generates a command class in `Classes/Command/` with the
   suffix `Command` and a Symfony `#[AsCommand]` attribute.
   **Arguments**:
    - `extension`: The target extension key (e.g., `my_extension`). Optional.
    - `command`: The command class name (e.g., `AwesomeCommand`). Must end with
      `Command`. Optional.
    - `namespace`: The namespace prefix (e.g., `Vendor\Extension\`). Optional.
    - `command-name`: The Symfony command name (e.g., `myext:awesome`). Must
      follow `namespace:command` format. Optional; defaults to
      `extension:command` (lowercase).
    - `command-description`: The command description (e.g.,
      `Executes awesome action`). Optional; defaults to a generic description.
      **Example**:
   ```bash
   vendor/bin/typo3 spawn:command my_extension AwesomeCommand Vendor\Extension\ myext:awesome "Executes awesome action"
   ```

3. **`spawn:middleware`**
   Creates a new TYPO3 middleware class.
   **Description**: Generates a middleware class in `Classes/Middleware/` with
   the suffix `Middleware`.
   **Arguments**:
    - `extension`: The target extension key. Optional.
    - `middleware`: The middleware class name (e.g., `AwesomeMiddleware`). Must
      end with `Middleware`. Optional.
    - `namespace`: The namespace prefix. Optional.
      **Example**:
   ```bash
   vendor/bin/typo3 spawn:middleware my_extension AwesomeMiddleware Vendor\Extension\
   ```

### Usage Notes

- **Interactive Mode**: If optional arguments are omitted, the commands prompt
  interactively for input, offering sensible defaults (e.g., extension selection
  from available extensions, default class names like `AwesomeController`).
- **Validation**: Class names are validated to follow TYPO3 conventions (e.g.,
  `XxxxController` for controllers, `namespace:command` for command names).
- **Post-Generation Hooks**: Some commands trigger additional actions (e.g.,
  updating `Services.yaml` for commands, middleware, repositories, and events;
  updating TCA for models).

## Requirements

- TYPO3 v13 or higher.
- PHP 8.3 or higher.
- Composer for installation.

## Contributing

Contributions are welcome! Please submit issues or pull requests to
the [repository](https://github.com/ralessandri/spawn).

## License

This extension is licensed under the MIT License.
