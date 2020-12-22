import { DataResponse, DataQuery } from "foodget/data";

// @todo
const BASE_URI = "http://localhost:8642/";
const BASE_URI_API = BASE_URI + "api/";

/**
 * We are not going to do anything more with this for now.
 */
export type Uuid = string;

/**
 * We have no monetary type for now, but we will.
 */
export type CurrencyAmount = number

/**
 * Filter query hashmap type definition.
 */
export interface UrlQueryString {
    [details: string]: string;
}

/**
 * Build URL.
 */
export function url(path: string, query?: UrlQueryString, baseUri?: string): string {
    if (!baseUri) {
        baseUri = BASE_URI;
    }
    if (!baseUri.endsWith('/')) {
        baseUri += '/';
    }
    if (query) {
        const components = [];
        for (let key in query) {
            components.push(encodeURIComponent(key) + "=" + encodeURIComponent(query[key]));
        }
        return baseUri + path + "?" + components.join("&");
    }
    return baseUri + path;
}

/**
 * Format currency value.
 */
export function formatCurrency(value?: CurrencyAmount): string {
    if (value === undefined) {
        return '';
    }
    return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' }).format(value);
}

/**
 * Format currency value.
 */
export function formatDate(value?: string | Date): string {
    if (!value) {
        return '';
    }
    if (typeof value === "string") {
        value = new Date(value);
    }
    return value.getFullYear() + "-" + (value.getMonth() + 1).toString().padStart(2, '0') + "-" + value.getDate().toString().padStart(2, '0');
}

/**
 * Just trust the server to speak the same language as us and send the data
 * query as-is.
 */
export function fetchList<T>(route: string, query?: DataQuery<T>): Promise<DataResponse<T>> {
    return fetch(
        url(route, {}, BASE_URI_API),
        {
            method: 'POST',
            mode: 'cors',
            cache: 'default',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json'
            },
            redirect: 'follow',
            referrerPolicy: 'no-referrer',
            body: JSON.stringify(query)
        })
        .then(response => response.json())
    ;
}

export enum CommandType {
    QuittanceAcquitte = "G.App.Location.QuittanceAcquitteCommand",
    QuittanceAcquitteCourrier = "G.App.Location.QuittanceGenerateCourrierCommand",
}

/**
 * Just trust the server to speak the same language as us and send the data
 * query as-is.
 */
export function sendCommand<T>(type: CommandType, content: any): Promise<any> {
    return fetch(
        url("dispatch", {type: type}, BASE_URI_API),
        {
            method: 'POST',
            mode: 'cors',
            cache: 'default',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json'
            },
            redirect: 'follow',
            referrerPolicy: 'no-referrer',
            body: JSON.stringify(content)
        })
    ;
}

