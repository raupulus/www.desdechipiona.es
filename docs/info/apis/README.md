# Integración de APIs en la Aplicación

*Última actualización: 2026-11-04*

> Este directorio documenta cómo nuestra aplicación utiliza e integra APIs de terceros y servicios externos.

---

## Directriz de Documentación de APIs

- **Documentación oficial externa**: La documentación técnica oficial tal cual la provee el proveedor externo se almacena en `docs/apis/` para servir de referencia estática.
- **Documentación de integración en la app (aquí en `docs/info/apis/`)**: Describe la arquitectura específica de cómo nuestra aplicación consume e implementa dichas APIs (servicios, clientes HTTP, jobs en cola, webhooks, variables de entorno requeridas y modelos de datos).
- **Referencias cruzadas**: Cada documento en `docs/info/apis/` debe enlazar a la documentación oficial correspondiente en `docs/apis/`.

---

## Índice de Integraciones

- [API Meteorología y Mareas (`api.raupulus.dev`)](weather-raupulus.md) — Consumo de parte meteorológico oficial de Chipiona, estación local y tabla diaria de mareas para corrales de pesca.
