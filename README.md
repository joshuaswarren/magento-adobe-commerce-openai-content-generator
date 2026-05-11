# Magento/Adobe Commerce OpenAI Content Generator

Magento 2 / Adobe Commerce module for generating product descriptions, short descriptions, meta titles, meta descriptions, and meta keywords from product attributes using OpenAI.

This is a personal fork of the original Creatuity OpenAI content generator module. The PHP namespace and module name are intentionally unchanged for compatibility with existing installs:

- `Creatuity_AIContentOpenAI`
- `Creatuity\AIContentOpenAI`

## What This Fork Updates

- Rebrands the package for the Magento/Adobe Commerce OpenAI Content Generator relaunch.
- Updates the default OpenAI model from `gpt-3.5-turbo` to `gpt-5.4-mini`.
- Removes old `text-davinci-*` completion models from the admin model selector.
- Adds current GPT-5.4/GPT-5.5 family models to the built-in selector.
- Allows admins to enable model-list fetching from OpenAI's `/v1/models` endpoint.
- Allows admins to enter a custom model ID for snapshots, fine-tuned models, or newly released models.
- Keeps the OpenAI API key in Magento's encrypted configuration field.
- Expands the Composer PHP constraint through PHP 8.3.

OpenAI currently recommends the Responses API for new applications, but this module still uses Chat Completions because the existing Magento integration and dependency library are built around that endpoint. A future larger release should move the provider layer to the Responses API after the core and mass-action modules are updated together.

The default model is `gpt-5.4-mini`, which is current, supports Chat Completions, and is better suited to high-volume catalog content generation than the older `gpt-4o-mini` default.

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
- `Fetch Model List From API`
- `Model Name`
- `Custom Model ID`

The OpenAI API key is stored using Magento's encrypted config backend. Do not commit environment-specific API keys to this repository.

### Model Selection

The built-in model selector includes:

- `gpt-5.4-mini`
- `gpt-5.5`
- `gpt-5.4`
- `gpt-5.4-nano`
- `gpt-4o`
- `gpt-4o-mini`
- `gpt-4`

When `Fetch Model List From API` is enabled, the selector is merged with likely Chat Completions model IDs returned by OpenAI's `/v1/models` endpoint. OpenAI's model-list endpoint only returns basic metadata, not endpoint capability metadata, so the module filters obvious non-chat models but still lets OpenAI validate the final model at generation time.

To use a model that is not in the selector, choose `Custom model ID` and enter the exact model ID in `Custom Model ID`.

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
