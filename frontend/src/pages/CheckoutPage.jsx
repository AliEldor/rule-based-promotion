import React, { useState, useEffect } from "react";
import { apiService } from "../services/api";
import { formatCurrency, calculateDiscountPercentage } from "../utils/helpers";
import { LoadingSpinner, ErrorMessage } from "../components/ui";
import {
  ShoppingCart,
  Package,
  Users,
  Calculator,
  Receipt,
  Tag,
  TrendingDown,
  AlertCircle,
  Crown,
  Star,
  Award,
} from "lucide-react";

const CheckoutPage = () => {
  const [products, setProducts] = useState([]);
  const [customers, setCustomers] = useState([]);
  const [selectedProduct, setSelectedProduct] = useState(null);
  const [quantity, setQuantity] = useState(1);
  const [selectedCustomer, setSelectedCustomer] = useState(null);
  const [evaluation, setEvaluation] = useState(null);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState(null);
  const [dataLoading, setDataLoading] = useState(true);

  useEffect(() => {
    loadInitialData();
  }, []);

  const loadInitialData = async () => {
    try {
      setDataLoading(true);
      const [productsResponse, customersResponse] = await Promise.all([
        apiService.getProducts(),
        apiService.getCustomers(),
      ]);

      setProducts(productsResponse.data || []);
      setCustomers(customersResponse.data || []);
    } catch (err) {
      setError(
        "Failed to load initial data. Please make sure the backend is running."
      );
      console.error("Error loading initial data:", err);
    } finally {
      setDataLoading(false);
    }
  };

  useEffect(() => {
    if (selectedProduct && selectedCustomer && quantity > 0) {
      evaluateCart();
    } else {
      setEvaluation(null);
    }
  }, [selectedProduct, selectedCustomer, quantity]);

  const evaluateCart = async () => {
    if (!selectedProduct || !selectedCustomer || quantity <= 0) return;

    setLoading(true);
    setError(null);

    try {
      const cartData = {
        line: {
          productId: selectedProduct.id,
          quantity: quantity,
          unitPrice: parseFloat(selectedProduct.price),
          categoryId: selectedProduct.categoryId || 10,
        },
        customer: {
          id: selectedCustomer.id,
          email: selectedCustomer.email,
          type: selectedCustomer.type,
          loyaltyTier:
            selectedCustomer.loyaltyTier || selectedCustomer.loyalty_tier,
          ordersCount:
            selectedCustomer.ordersCount || selectedCustomer.orders_count || 0,
          city: selectedCustomer.city,
        },
        orderReference: `ORDER-${Date.now()}`,
      };

      // console.log("Selected Product:", selectedProduct);
      // console.log("Selected Customer:", selectedCustomer);
      // console.log("Sending cart data:", cartData);

      const result = await apiService.evaluateCart(cartData);
      setEvaluation(result.data);
    } catch (err) {
      console.error("Error evaluating cart:", err);
      console.error("Error response:", err.response?.data);
      setError("Failed to evaluate cart. Please try again.");
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="min-h-screen bg-gray-50 py-8">
      <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="text-center mb-8">
          <div className="flex items-center justify-center mb-4">
            <div className="flex items-center justify-center w-16 h-16 bg-blue-100 rounded-xl">
              <ShoppingCart className="h-8 w-8 text-blue-600" />
            </div>
          </div>
          <h1 className="text-3xl font-bold text-gray-900 mb-2">
            Rule-Based Promotion Engine
          </h1>
          <p className="text-gray-600">
            Select products and customers to see dynamic discount calculations
          </p>
        </div>

        {dataLoading ? (
          <div className="flex items-center justify-center py-12">
            <LoadingSpinner
              size="lg"
              text="Loading products and customers..."
            />
          </div>
        ) : error ? (
          <div className="mb-6">
            <ErrorMessage message={error} onRetry={loadInitialData} />
          </div>
        ) : (
          <>
            <div className="grid md:grid-cols-2 gap-8">
              {/* Left Column - Product Selection */}
              <div className="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
                <div className="flex items-center mb-6">
                  <div className="flex items-center justify-center w-10 h-10 bg-blue-100 rounded-lg mr-3">
                    <Package className="h-5 w-5 text-blue-600" />
                  </div>
                  <h2 className="text-xl font-semibold text-gray-900">
                    Product Selection
                  </h2>
                </div>

                {/* Product Selection */}
                <div className="mb-6">
                  <label className="block text-sm font-medium text-gray-700 mb-2">
                    Choose Product
                  </label>
                  <select
                    value={selectedProduct?.id || ""}
                    onChange={(e) => {
                      const product = products.find(
                        (p) => p.id === parseInt(e.target.value)
                      );
                      setSelectedProduct(product);
                    }}
                    className="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                  >
                    <option value="">Select a product...</option>
                    {products.map((product) => (
                      <option key={product.id} value={product.id}>
                        {product.name} - {formatCurrency(product.price)}
                      </option>
                    ))}
                  </select>
                </div>

                {/* Quantity Selection */}
                <div className="mb-6">
                  <label className="block text-sm font-medium text-gray-700 mb-2">
                    Quantity
                  </label>
                  <input
                    type="number"
                    min="1"
                    max="20"
                    value={quantity}
                    onChange={(e) => setQuantity(parseInt(e.target.value) || 1)}
                    className="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                  />
                </div>

                {/* Customer Selection */}
                <div className="mb-6">
                  <label className="block text-sm font-medium text-gray-700 mb-2">
                    Customer
                  </label>
                  <select
                    value={selectedCustomer?.id || ""}
                    onChange={(e) => {
                      const customer = customers.find(
                        (c) => c.id === parseInt(e.target.value)
                      );
                      setSelectedCustomer(customer);
                    }}
                    className="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                  >
                    <option value="">Select a customer...</option>
                    {customers.map((customer) => (
                      <option key={customer.id} value={customer.id}>
                        {customer.name} •{" "}
                        {customer.type === "restaurants"
                          ? "Restaurant"
                          : "Retail"}{" "}
                        •{" "}
                        {customer.loyalty_tier === "none"
                          ? "Standard"
                          : customer.loyalty_tier.charAt(0).toUpperCase() +
                            customer.loyalty_tier.slice(1)}{" "}
                        Tier
                      </option>
                    ))}
                  </select>
                </div>

                {/* Selected Product Info */}
                {selectedProduct && (
                  <div className="bg-gray-50 rounded-md p-4">
                    <h3 className="text-sm font-medium text-gray-900 mb-2">
                      Selected Product
                    </h3>
                    <div className="text-sm text-gray-600">
                      <p>
                        <span className="font-medium">Product:</span>{" "}
                        {selectedProduct.name}
                      </p>
                      <p>
                        <span className="font-medium">Unit Price:</span>{" "}
                        {formatCurrency(selectedProduct.price)}
                      </p>
                      <p>
                        <span className="font-medium">Subtotal:</span>{" "}
                        {formatCurrency(selectedProduct.price * quantity)}
                      </p>
                    </div>
                  </div>
                )}
              </div>

              {/* Right Column - Discount Calculation */}
              <div className="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
                <div className="flex items-center mb-6">
                  <div className="flex items-center justify-center w-10 h-10 bg-green-100 rounded-lg mr-3">
                    <Calculator className="h-5 w-5 text-green-600" />
                  </div>
                  <h2 className="text-xl font-semibold text-gray-900">
                    Discount Calculation
                  </h2>
                </div>

                {loading && (
                  <div className="flex items-center justify-center py-8">
                    <LoadingSpinner text="Calculating discounts..." />
                  </div>
                )}

                {!selectedProduct || !selectedCustomer ? (
                  <div className="text-center py-8 text-gray-500">
                    <AlertCircle className="mx-auto h-12 w-12 text-gray-400 mb-4" />
                    <p>
                      Select a product and customer to see discount calculations
                    </p>
                  </div>
                ) : evaluation ? (
                  <div className="space-y-4">
                    {/* Order Summary */}
                    <div className="bg-gray-50 rounded-md p-4">
                      <h3 className="text-sm font-medium text-gray-900 mb-3">
                        Order Summary
                      </h3>
                      <div className="space-y-2 text-sm">
                        <div className="flex justify-between">
                          <span>Original Total:</span>
                          <span>
                            {formatCurrency(evaluation.originalLineTotal)}
                          </span>
                        </div>
                        <div className="flex justify-between text-green-600">
                          <span>Total Discount:</span>
                          <span>
                            -{formatCurrency(evaluation.totalDiscount)}
                          </span>
                        </div>
                        <hr className="my-2" />
                        <div className="flex justify-between font-semibold text-lg">
                          <span>Final Total:</span>
                          <span>
                            {formatCurrency(evaluation.finalLineTotal)}
                          </span>
                        </div>
                      </div>
                    </div>

                    {/* Applied Rules */}
                    {evaluation.applied && evaluation.applied.length > 0 ? (
                      <div>
                        <div className="flex items-center mb-3">
                          <Tag className="h-4 w-4 text-gray-600 mr-2" />
                          <h3 className="text-sm font-medium text-gray-900">
                            Applied Promotions
                          </h3>
                        </div>
                        <div className="space-y-2">
                          {evaluation.applied.map((rule, index) => (
                            <div
                              key={index}
                              className="bg-green-50 border border-green-200 rounded-md p-3"
                            >
                              <div className="flex justify-between items-start">
                                <div>
                                  <p className="text-sm font-medium text-green-800">
                                    {rule.ruleName}
                                  </p>
                                  <p className="text-xs text-green-600">
                                    Rule ID: {rule.ruleId}
                                  </p>
                                </div>
                                <span className="text-sm font-semibold text-green-800">
                                  -{formatCurrency(rule.discount)}
                                </span>
                              </div>
                            </div>
                          ))}
                        </div>
                      </div>
                    ) : (
                      <div className="bg-yellow-50 border border-yellow-200 rounded-md p-4">
                        <p className="text-sm text-yellow-800">
                          No promotions applied to this order.
                        </p>
                      </div>
                    )}

                    {/* Savings Badge */}
                    {evaluation.totalDiscount > 0 && (
                      <div className="bg-green-100 border border-green-300 rounded-md p-4 text-center">
                        <p className="text-lg font-bold text-green-800">
                          You saved {formatCurrency(evaluation.totalDiscount)}!
                        </p>
                        <p className="text-sm text-green-600">
                          {calculateDiscountPercentage(
                            evaluation.originalLineTotal,
                            evaluation.totalDiscount
                          )}
                          % off your order
                        </p>
                      </div>
                    )}
                  </div>
                ) : null}
              </div>
            </div>

            {/* Customer Info */}
            {selectedCustomer && (
              <div className="mt-8 bg-white rounded-xl shadow-lg p-6 border border-gray-100">
                <div className="flex items-center mb-4">
                  <div className="flex items-center justify-center w-10 h-10 bg-purple-100 rounded-lg mr-3">
                    <Users className="h-5 w-5 text-purple-600" />
                  </div>
                  <h2 className="text-xl font-semibold text-gray-900">
                    Customer Information
                  </h2>
                </div>
                <div className="grid md:grid-cols-3 gap-4 text-sm">
                  <div>
                    <p className="font-medium text-gray-700">Name</p>
                    <p className="text-gray-600">{selectedCustomer.name}</p>
                  </div>
                  <div>
                    <p className="font-medium text-gray-700">Type</p>
                    <p className="text-gray-600 capitalize">
                      {selectedCustomer.type}
                    </p>
                  </div>
                  <div>
                    <p className="font-medium text-gray-700">Loyalty Tier</p>
                    <div className="mt-1">
                      {selectedCustomer.loyalty_tier === "gold" && (
                        <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                          <Crown className="h-3 w-3 mr-1" />
                          Gold Tier
                        </span>
                      )}
                      {selectedCustomer.loyalty_tier === "silver" && (
                        <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                          <Award className="h-3 w-3 mr-1" />
                          Silver Tier
                        </span>
                      )}
                      {selectedCustomer.loyalty_tier === "bronze" && (
                        <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                          <Award className="h-3 w-3 mr-1" />
                          Bronze Tier
                        </span>
                      )}
                      {selectedCustomer.loyalty_tier === "none" && (
                        <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                          <Star className="h-3 w-3 mr-1" />
                          Standard Tier
                        </span>
                      )}
                    </div>
                  </div>
                  <div>
                    <p className="font-medium text-gray-700">Orders Count</p>
                    <p className="text-gray-600">
                      {selectedCustomer.orders_count}
                    </p>
                  </div>
                  <div>
                    <p className="font-medium text-gray-700">City</p>
                    <p className="text-gray-600">{selectedCustomer.city}</p>
                  </div>
                  <div>
                    <p className="font-medium text-gray-700">Email</p>
                    <p className="text-gray-600">{selectedCustomer.email}</p>
                  </div>
                </div>
              </div>
            )}
          </>
        )}
      </div>
    </div>
  );
};

export default CheckoutPage;
