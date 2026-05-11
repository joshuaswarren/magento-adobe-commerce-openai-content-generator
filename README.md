# Magento/Adobe Commerce OpenAI Content Generator

Magento 2 / Adobe Commerce module for generating product descriptions, short descriptions, meta titles, meta descriptions, and meta keywords from product attributes using OpenAI.

This is a personal fork of the original Creatuity OpenAI content generator module. The PHP namespace and module name are intentionally unchanged for compatibility with existing installs:

- `Creatuity_AIContentOpenAI`
- `Creatuity\AIContentOpenAI`

## What This Fork Updates

- Rebrands the package for the Magento/Adobe Commerce OpenAI Content Generator relaunch.
- Updates the default OpenAI model from `gpt-3.5-turbo` to `gpt-4o-mini`.
- Removes old `text-davinci-*` completion models from the admin model selector.
- Keeps `gpt-4o-mini`, `gpt-4o`, and `gpt-4` as selectable Chat Completions models.
- Keeps the OpenAI API key in Magento's encrypted configuration field.
- Expands the Composer PHP constraint through PHP 8.3.

OpenAI currently recommends the Responses API for new applications, but this module still uses Chat Completions because the existing Magento integration and dependency library are built around that endpoint. A future larger release should move the provider layer to the Responses API after the core and mass-action modules are updated together.

## Installation

Until this fork is published to Packagist, install it through a VCS repository entry:

```json
{
    "repositories": {
        "magento-adobe-commerce-openai-content-generator": {
            "type": "vcs",
            "url": "git@github.com:joshuaswarren/magento-adobe-commerce-openai-content-generator.git"
        },
        "magento2-ai-content-generator-core": {
            "type": "vcs",
            "url": "git@github.com:creatuity/magento2-ai-content-generator-core.git"
        },
        "magento2-ai-content-generator-mass-actions": {
            "type": "vcs",
            "url": "git@github.com:creatuity/magento2-ai-content-generator-mass-actions.git"
        }
    }
}
```

Then require the package:

```bash
composer require joshuaswarren/magento-adobe-commerce-openai-content-generator:^0.1
bin/magento setup:upgrade
```

## Configuration

In the Magento admin, go to `Stores > Configuration > Creatuity > AI Content`.

Configure:

- `Enabled`
- `AI Provider`: OpenAI
- `Description Attributes`
- `Meta-tags Attributes`
- `OpenAI API Key`
- `Model Name`

The OpenAI API key is stored using Magento's encrypted config backend. Do not commit environment-specific API keys to this repository.

## Usage

After configuring and saving a product, use the `Generate With AI` button in the product edit screen to generate:

- short description
- description
- meta title
- meta description
- meta keywords

Mass generation remains available from the product grid through the mass actions module.

## Related Repositories

- Original OpenAI integration: https://github.com/joshuaswarren/magento-adobe-commerce-openai-content-generator
- Core module: https://github.com/creatuity/magento2-ai-content-generator-core
- Mass actions module: https://github.com/creatuity/magento2-ai-content-generator-mass-actions

## License

The upstream package declares a proprietary license. Confirm licensing before publishing this fork to Packagist or distributing it outside approved use.
