export const formatCurrency = (amount, currency = "USD") => {
  return new Intl.NumberFormat("en-US", {
    style: "currency",
    currency: currency,
  }).format(amount);
};

export const formatDate = (dateString, options = {}) => {
  if (!dateString) return "N/A";

  const defaultOptions = {
    year: "numeric",
    month: "short",
    day: "numeric",
    ...options,
  };

  return new Date(dateString).toLocaleDateString("en-US", defaultOptions);
};

export const calculateDiscountPercentage = (originalAmount, discountAmount) => {
  if (!originalAmount || originalAmount === 0) return 0;
  return Math.round((discountAmount / originalAmount) * 100 * 10) / 10;
};

export const capitalizeWords = (str) => {
  return str
    .split(" ")
    .map((word) => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase())
    .join(" ");
};

export const truncateText = (text, maxLength) => {
  if (!text || text.length <= maxLength) return text;
  return text.slice(0, maxLength) + "...";
};

export const isValidJSON = (str) => {
  try {
    JSON.parse(str);
    return true;
  } catch (e) {
    return false;
  }
};

export const formatRuleData = (data) => {
  if (!data) return "N/A";
  if (typeof data === "string") return data;
  if (typeof data === "object") {
    try {
      return JSON.stringify(data, null, 2);
    } catch (e) {
      return "Invalid format";
    }
  }
  return String(data);
};
