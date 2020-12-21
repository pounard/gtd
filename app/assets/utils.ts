import { DataResponse, DataQuery } from "foodget/data";

// @todo
const BASE_URI = "http://localhost:8642/api/";

/**
 * We are not going to do anything more with this for now.
 */
export type Uuid = string;

/**
 * We have no monetary type for now, but we will.
 */
export type CurrencyAmount = number

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
        BASE_URI + route,
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
}

/**
 * Just trust the server to speak the same language as us and send the data
 * query as-is.
 */
export function sendCommand<T>(type: CommandType, content: any): Promise<any> {
    return fetch(
        BASE_URI + "dispatch?type=" + encodeURIComponent(type),
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

