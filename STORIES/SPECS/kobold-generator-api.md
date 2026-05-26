# Kobold Generator API

This document outlines the API for the Kobold Generator, a tool designed to generate text based on a defined grammar. 
The API provides a simple interface for users to create and utilize grammars for text generation.

## Endpoint 

### POST /generate-kobold
#### Description
Generates text based on the grammar grm/kobold_json_it.grm. The grammar is present as a file in the project and is loaded by the API to produce output.
#### Request Body
User can pass in the body the seed for the random generator, which will produce the same output for the same seed. If no seed is provided, a random seed will be used.
```json
{
  "seed": "optional-seed-value",
  "language": "optional-language-code" 
}
```
The default language is Italian, but the API can support multiple languages if the grammar is designed to do so. The language parameter allows users to specify which language to generate text in, if the grammar supports it.

#### Response
The response will be a JSON object containing the generated text.
grm/kobold_json_xx.grm is already designed to produce output in json format. The API will return the generated JSON directly in the body of the response.

### Multilanguage Support
The API is designed to support multiple languages, but this depends on the presence of the grammar file:
the api will search for a grammar file named kobold_json_{language_code}.grm (e.g. kobold_json_en.grm for English, kobold_json_it.grm for Italian) and use it if it exists. If no language parameter is provided, or if the specified language grammar does not exist, the API will default to using kobold_json.grm.
grm/kobold_json_it.grm is the default grammar file that will be used if no language is specified or if the specified language grammar does not exist. It is designed to produce output in Italian, but can be modified to support other languages as needed.
 
