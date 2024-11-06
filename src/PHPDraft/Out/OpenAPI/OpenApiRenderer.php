<?php

namespace PHPDraft\Out\OpenAPI;

use PHPDraft\Model\Elements\BasicStructureElement;
use PHPDraft\Model\Elements\ElementStructureElement;
use PHPDraft\Model\Elements\StructureElement;
use PHPDraft\Model\HTTPRequest;
use PHPDraft\Model\HTTPResponse;
use PHPDraft\Model\Resource;
use PHPDraft\Model\Transition;
use PHPDraft\Out\BaseTemplateRenderer;

class OpenApiRenderer extends BaseTemplateRenderer {

    public function init(object $json): self
    {
        $this->object = $json;

        return $this;
    }

    public function write(string $filename): void
    {
        $output = json_encode($this->toOpenApiObject(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        file_put_contents($filename, $output);
    }

    /**
     * Get OpenAPI base structure.
     *
     * @return array<string,object|array<string,mixed>|string[]>
     */
    private function toOpenApiObject(): array
    {
        $this->parse_base_data($this->object);

        return [
                'openapi' => '3.1.0',
//                'jsonSchemaDialect' => '',
                'info' => $this->getApiInfo(),
                'servers' => $this->getServers(),
                'paths' => $this->getPaths(),
                'webhooks' => $this->getWebhooks(),
                'components' => $this->getComponents(),
                'security' => $this->getSecurity(),
                'tags' => $this->getTags(),
//                'externalDocs' => $this->getDocs(),
        ];
    }

    /**
     * Get generic info for the API
     * @return array<string,string>
     */
    private function getApiInfo(): array {
        return [
            "title"=> $this->base_data['TITLE'],
            "version"=> $this->base_data['VERSION'] ?? '1.0.0',
            "summary"=> $this->base_data['TITLE'] . ' generated from API Blueprint',
            "description"=> $this->base_data['DESC'],
//            "termsOfService"=> "https://example.com/terms/",
//            "contact"=> [
//                "name"=> "API Support",
//                "url"=> "https://www.example.com/support",
//                "email"=> "support@example.com"
//           ],
//            "license" => [
//                    "name"=> "Apache 2.0",
//                    "url"=> "https://www.apache.org/licenses/LICENSE-2.0.html"
//            ],
        ];
    }

    /**
     * Get information about the servers involved in the API.
     *
     * @return array<array<string,string>>
     */
    private function getServers(): array {
        $return = [];
        $return[] = ['url' => $this->base_data['HOST'], 'description' => 'Main host'];

        foreach (explode(',', $this->base_data['ALT_HOST'] ?? '') as $host) {
            $return[] = ['url' => $host];
        }

        return $return;
    }

    /**
     * Get path information
     *
     * @return object
     */
    private function getPaths(): object {
        $return = [];
        foreach ($this->categories as $category) {
            /** @var Resource $resource */
            foreach ($category->children ?? [] as $resource) {
                /** @var Transition $transition */
                foreach ($resource->children ?? [] as $transition) {
                    $transition_return = [];
                    $parameters = [];
                    if ($transition->url_variables !== []) {
                        $parameters += $this->toParameters($transition->url_variables, $transition->href);
                    }
                    if ($transition->data_variables !== NULL)
                    {
                        $parameters += $this->toParameters([$transition->data_variables], $transition->href);
                    }
                    if ($transition->structures !== [])
                    {
                        $parameters += $this->toParameters($transition->structures, $transition->href);
                    }
                    if ($parameters !== [])
                    {
                        $transition_return['parameters'] = $parameters;
                    }

                    /** @var HTTPRequest $request */
                    foreach ($transition->requests as $request)
                    {
                        $request_return = $this->toOperation(request: $request, transition: $transition, tags: [$category->title]);
                        $request_return['responses'] = $this->toResponses($transition->responses);
                        $transition_return[strtolower($request->method)] = (object) $request_return;
                    }
                    $return[$transition->href] = (object) $transition_return;
                }
            }
        }

        return (object) $return;
    }

    /**
     * @param HTTPRequest $request
     * @param Transition  $transition
     * @param string[]    $tags
     *
     * @return array<string,mixed>
     */
    private function toOperation(HTTPRequest $request, Transition $transition, array $tags): array {
        $operation = [
            'operationId' => $request->get_id(),
            'summary' => $request->title ?? $transition->title,
            'tags' => $tags,
        ];
        $description = $request->description ?? $transition->description;
        if ($description !== NULL)
        {
            $operation['description'] = $description;
        }

        $parameters = [];
        if ($request->struct !== NULL) {
            if (is_array($request->struct))
            {
                $parameters += $this->toParameters($request->struct, $transition->href);
            } else {
                $parameters += $this->toParameters([$request->struct], $transition->href);
            }
        }

        foreach ($request->headers as $name => $value) {
            if ($name === 'Content-Type') { continue; }
            if ($name === $this->base_data['API_KEY_HEADER'] ?? NULL) {
                $operation['security'] = [["api_key" => []]];
                continue;
            }

            $parameters[] = [
                'name' => $name,
                'in' => 'header',
                'schema' => ['type' => 'string'],
                'example' => $value
            ];
        }

        if ($parameters !== []) {
            $operation['parameters'] = $parameters;
        }

        $body = $this->toBody($request);
        if ($body !== []) {
            $body['required'] = TRUE;
            $operation['requestBody'] = $body;
        }

        return $operation;
    }

    /**
     * Convert objects into parameters.
     *
     * @param BasicStructureElement[] $objects List of objects to convert
     * @param string                  $href    Base URL
     *
     * @return array<array<string,mixed>>
     */
    private function toParameters(array $objects, string $href): array {
        $return = [];

        foreach ($objects as $variable) {
            if ($variable->key === NULL) { continue; }

            $return_tmp = [
                    'name' => $variable->key->value,
                    'in'   => str_contains($href, '{' . $variable->key->value . '}') ? 'path' : 'query',
                    'required' => in_array('required', $variable->status, TRUE),
                    'schema' => [],
            ];
            if ($this->isRef($variable->type)) {
                $return_tmp['schema']['$ref'] = '#/components/schemas/' . $variable->type;
            } else {
                $return_tmp['schema']['type'] = $variable->type;
            }

            if (isset($variable->value))
            {
                $return_tmp['example'] = $variable->value;
            }

            if (isset($variable->description))
            {
                $return_tmp['description'] = $variable->description;
            }
            $return[] = $return_tmp;
        }

        return $return;
    }

    private function isRef(string $type): bool
    {
        return !in_array($type, ["array", "boolean", "integer", "null", "number", "object", "string"], TRUE);
    }

    /**
     * Convert responses to the OpenAPI structure
     *
     * @param HTTPResponse[] $responses List of responses to parse
     *
     * @return array<int,array<string,mixed>> List of status codes with the response
     */
    private function toResponses(array $responses): array
    {
        $return = [];

        foreach ($responses as $response) {
            $headers = [];
            foreach ($response->headers as $header => $value) {
                if ($header === 'Content-Type') { continue; }
                $headers[$header] = [
                    'schema' => [
                        'type' => 'string',
                        'example' => $value,
                    ],
                ];
            }

            $content = [];
            foreach ($response->content as $key => $contents) {
                $content[$key] = [
                    'schema' => [
                        'type' => "string",
                    ],
                    'examples' => [
                            'base' => [
                                    'value' => $contents,
                            ],
                    ],
                ];
            }

            foreach ($response->structure as $structure) {
                if ($structure->key === NULL) { continue; }
                $content[$response->headers['Content-Type'] ?? 'text/plain'] = ['schema' => $this->getComponent($structure)];
            }

            $return[$response->statuscode] = [
                    'description' => $response->description ?? $response->title ?? '',
                    'headers' => (object) $headers,
                    'content' => (object) $content,
            ];
        }

        return $return;
    }

    /**
     * Convert a HTTP Request into an OpenAPI body
     *
     * @param HTTPRequest $request Request to convert
     *
     * @return array<string,array<string,mixed>> OpenAPI style body
     */
    private function toBody(HTTPRequest $request): array
    {
        $return = [];

        if (!is_array($request->struct) && $request->struct->description !== NULL) {
            $return['description'] = $request->struct->description;
        }

        $content_type = $request->headers['Content-Type'] ?? 'text/plain';
        if (isset($request->struct) && $request->struct !== [])
        {
            $content = $this->getComponent($request->struct);
            unset($content['required']);
            $return['content'] = [
                $content_type => ['schema' => $content],
            ];
        } else {
//            $return['content'] = [
//                    $content_type => [
//                            'schema' => [
//                                    'type' => 'string',
//                            ],
//                    ],
//            ];
        }

        if ($request->body !== NULL && $request->body !== []) {
            $return['content'][$content_type]['examples']['base']['value'] = $request->body[0];
        }

        return $return;
    }

    /**
     * Get webhook information for the API.
     * @return object
     */
    private function getWebhooks(): object { return (object) []; }

    /**
     * Get component information for the API.
     * @return object
     */
    private function getComponents(): object {
        $return = [];
        foreach ($this->base_structures as $structure)
        {
          $object = $this->getComponent($structure);

          if ($structure->ref !== NULL) {
            $return[$structure->type] = [
              'allOf' => [
                ['$ref' => "#/components/schemas/$structure->ref"],
                $object,
              ],
            ];
          } else {
            $return[$structure->type] = $object;
          }
        }
        $return_object = ['schemas' => $return ];
        if (isset($this->base_data['API_KEY_HEADER'])) {
            $return_object['securitySchemes'] = [
                'api_key' => [
                    "type" => "apiKey",
                    "name" => $this->base_data['API_KEY_HEADER'],
                    "in" => "header",
                ]
            ];
        }

        return (object) $return_object;
    }

  /**
   * Get a component
   *
   * @param BasicStructureElement $structure
   *
   * @return array<string, mixed>
   */
    private function getComponent(BasicStructureElement $structure): array
    {
      $required = [];
      $properties = [];
      if (is_array($structure->value))
      {
        /** @var BasicStructureElement $value */
        foreach ($structure->value as $value)
        {
          $propery_data = $this->getSchemaProperty($value);
          if ($propery_data === NULL) { continue; }
          if (in_array('required', $value->status, TRUE)) { $required[] = $value->key->value;}

          $properties[$value->key->value] = $propery_data;
        }
      }

      $object = [
        'type' => $structure->element,
      ];
      switch ($structure->element) {
          case 'enum':
          case 'array':
              $object['items'] = $properties;
              break;
          case 'object':
              $object['properties'] = $properties;
              $object['required'] = $required;
              break;
          case 'member':
            //TODO: Check this case
            break;
        default:
            break;
      }

      if ($structure->description !== NULL) {
        $object['description'] = $structure->description;
      }

      return $object;
    }

  /**
   * Get property in a schema
   *
   * @param BasicStructureElement|ElementStructureElement $value Data to convert
   *
   * @return array<string,mixed>|null
   */
    private function getSchemaProperty(BasicStructureElement|ElementStructureElement $value): ?array
    {
      //TODO: Check this case
      if ($value instanceof ElementStructureElement || $value->key === NULL)
      {
        return NULL;
      }

      $propery_data = [];
      if ($value->description !== NULL) {
          $propery_data['description'] = $value->description;
      }

      if ($this->isRef($value->type) && $value->type !== 'enum')
      {
          $propery_data['$ref'] = '#/components/schemas/' . $value->type;
          return $propery_data;
      }

      if ($value->type === 'enum') {
        $propery_data['type'] = in_array('nullable', $value->status, TRUE) ? [ $value->type, 'null' ] : $value->type;
        $options = [];
        foreach ($value->value->value as $option) {
            if ($option instanceof ElementStructureElement) {
                $options[] = ['const' => $option->value, 'title' => $option->value];
            }
        }
        $propery_data['oneOf'] = $options;

        return $propery_data;
      }

      if ($value->type === 'array') {
        $propery_data['type'] = array_unique(array_map(fn($item) => $item->type,$value->value->value));
        $propery_data['example'] = array_merge(array_filter(array_map(fn($item) => $item->value,$value->value->value)));

        return $propery_data;
      }

      if ($value->type === 'object') {
        $propery_data['type'] = $value->type;
        $propery_data['properties'] = $this->getComponent($value->value)['properties'];

        return $propery_data;
      }

      $propery_data['type'] = in_array('nullable', $value->status, TRUE) ? [ $value->type, 'null' ] : $value->type;

      return $propery_data;
    }

    /**
     * Get security information for the API
     * @return array<array<string, array<string, mixed>>>
     */
    private function getSecurity(): array {
        if (isset($this->base_data['API_KEY_LOCK_ALL']) && filter_var($this->base_data['API_KEY_LOCK_ALL'], FILTER_VALIDATE_BOOLEAN)) {
            return [["api_key" => []]];
        }

        return [];
    }

//    private function getDocs(): object { return (object) []; }

    /**
     * Get tags for the API
     * @return array<array<string, string>>
     */
    private function getTags(): array {
        $return = [];
        foreach ($this->categories as $category) {
            $data = [
                'name' => $category->title,
            ];
            if ($category->description !== NULL) {
                $data['description'] = $category->description;
            }

            $return[] = $data;
        }

        return $return;
    }

}
