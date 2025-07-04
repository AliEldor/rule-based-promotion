import api from "../utils/axios";

export const apiService = {
  evaluateCart: async (cartData) => {
    try {
      const response = await api.post("/v1/evaluate", cartData);
      return response.data;
    } catch (error) {
      console.error("Error evaluating cart:", error);
      throw error;
    }
  },

  getRules: async () => {
    try {
      const response = await api.get("/v1/rules");
      return response.data;
    } catch (error) {
      console.error("Error fetching rules:", error);
      throw error;
    }
  },

  createRule: async (ruleData) => {
    try {
      const response = await api.post("/v1/rules", ruleData);
      return response.data;
    } catch (error) {
      console.error("Error creating rule:", error);
      throw error;
    }
  },

  getProducts: async () => {
    try {
      const response = await api.get("/v1/products");
      return response.data;
    } catch (error) {
      console.error("Error fetching products:", error);
      throw error;
    }
  },

  getCustomers: async () => {
    try {
      const response = await api.get("/v1/customers");
      return response.data;
    } catch (error) {
      console.error("Error fetching customers:", error);
      throw error;
    }
  },

  getCategories: async () => {
    try {
      const response = await api.get("/v1/categories");
      return response.data;
    } catch (error) {
      console.error("Error fetching categories:", error);
      throw error;
    }
  },
};
