import dayjs from "dayjs";

export function formatDate(date, format = "MM/DD/YYYY hh:mm A") {
    if (!date) return null;
    return dayjs(date).format(format);
}

export function WordformatDate(date, format = "MMMM DD, YYYY") {
    if (!date) return null;
    return dayjs(date).format(format);
}
export function formatCurrency(amount) {
    if (typeof amount !== "number") {
        amount = parseFloat(amount) || 0;
    }

    return new Intl.NumberFormat("en-PH", {
        style: "currency",
        currency: "PHP",
    }).format(amount);
}
