import React, { useState, useEffect } from "react";
import { apiService } from "../services/api";
import { LoadingSpinner, ErrorMessage } from "../components/ui";
import { useApi } from "../hooks/useApi";
import {
  Settings,
  FileText,
  CheckCircle,
  XCircle,
  Layers,
  Calendar,
  Hash,
  Tag,
  Percent,
  DollarSign,
  Gift,
  Users,
  Package,
  ShoppingCart,
} from "lucide-react";

const RulesManagementPage = () => {
  const [rules, setRules] = useState([]);
  const { loading, error, execute, clearError } = useApi();

  useEffect(() => {
    fetchRules();
  }, []);

  const fetchRules = async () => {
    try {
      const response = await execute(() => apiService.getRules());
      const rulesData = response.data?.data || response.data || [];
      setRules(rulesData);
    } catch (err) {
      console.error("Error fetching rules:", err);
    }
  };

  const formatDate = (dateString) => {
    if (!dateString) return "N/A";
    return new Date(dateString).toLocaleDateString();
  };

  const formatConditions = (conditions) => {
    if (!conditions || typeof conditions !== "object") {
      return <span className="text-gray-500 italic">No conditions</span>;
    }

    const conditionItems = [];

    if (conditions.quantity) {
      if (conditions.quantity.min) {
        conditionItems.push(
          <div key="quantity-min" className="flex items-center text-sm mb-1">
            <Package className="h-4 w-4 text-blue-500 mr-2" />
            <span>Min quantity: {conditions.quantity.min}</span>
          </div>
        );
      }
      if (conditions.quantity.max) {
        conditionItems.push(
          <div key="quantity-max" className="flex items-center text-sm mb-1">
            <Package className="h-4 w-4 text-blue-500 mr-2" />
            <span>Max quantity: {conditions.quantity.max}</span>
          </div>
        );
      }
    }

    if (conditions.customer) {
      if (conditions.customer.loyaltyTier) {
        conditionItems.push(
          <div key="loyalty-tier" className="flex items-center text-sm mb-1">
            <Users className="h-4 w-4 text-purple-500 mr-2" />
            <span>Loyalty tier: {conditions.customer.loyaltyTier}</span>
          </div>
        );
      }
      if (conditions.customer.type) {
        conditionItems.push(
          <div key="customer-type" className="flex items-center text-sm mb-1">
            <Users className="h-4 w-4 text-purple-500 mr-2" />
            <span>Customer type: {conditions.customer.type}</span>
          </div>
        );
      }
    }

    if (conditions.product) {
      if (conditions.product.categoryId) {
        conditionItems.push(
          <div key="category" className="flex items-center text-sm mb-1">
            <Tag className="h-4 w-4 text-green-500 mr-2" />
            <span>Category ID: {conditions.product.categoryId}</span>
          </div>
        );
      }
    }

    if (conditions.line) {
      if (conditions.line.total) {
        if (conditions.line.total.min) {
          conditionItems.push(
            <div
              key="line-total-min"
              className="flex items-center text-sm mb-1"
            >
              <ShoppingCart className="h-4 w-4 text-orange-500 mr-2" />
              <span>Min total: ${conditions.line.total.min}</span>
            </div>
          );
        }
        if (conditions.line.total.max) {
          conditionItems.push(
            <div
              key="line-total-max"
              className="flex items-center text-sm mb-1"
            >
              <ShoppingCart className="h-4 w-4 text-orange-500 mr-2" />
              <span>Max total: ${conditions.line.total.max}</span>
            </div>
          );
        }
      }
    }

    return conditionItems.length > 0 ? (
      <div className="space-y-1">{conditionItems}</div>
    ) : (
      <span className="text-gray-500 italic">No conditions</span>
    );
  };

  const formatActions = (actions) => {
    if (!actions || typeof actions !== "object") {
      return <span className="text-gray-500 italic">No actions</span>;
    }

    const getActionIcon = (type) => {
      switch (type) {
        case "percentage_discount":
          return <Percent className="h-4 w-4 text-red-500 mr-2" />;
        case "fixed_discount":
          return <DollarSign className="h-4 w-4 text-red-500 mr-2" />;
        case "free_units":
          return <Gift className="h-4 w-4 text-green-500 mr-2" />;
        default:
          return <Tag className="h-4 w-4 text-blue-500 mr-2" />;
      }
    };

    const getActionText = (actions) => {
      switch (actions.type) {
        case "percentage_discount":
          return `${actions.value}% discount`;
        case "fixed_discount":
          return `$${actions.value} off`;
        case "free_units":
          return `${actions.value} free unit${actions.value > 1 ? "s" : ""}`;
        default:
          return `${actions.type}: ${actions.value}`;
      }
    };

    return (
      <div className="flex items-center text-sm">
        {getActionIcon(actions.type)}
        <span>{getActionText(actions)}</span>
      </div>
    );
  };

  if (loading) {
    return (
      <div className="min-h-screen bg-gray-50 py-8">
        <div className="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex items-center justify-center py-12">
            <LoadingSpinner text="Loading rules..." />
          </div>
        </div>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-gray-50 py-8">
      <div className="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="mb-8">
          <div>
            <h1 className="text-3xl font-bold text-gray-900">
              Promotion Rules Management
            </h1>
            <p className="text-gray-600 mt-2">
              View and manage promotion rules in your system
            </p>
          </div>
        </div>

        {error && (
          <div className="mb-6">
            <div className="bg-red-50 border border-red-200 rounded-lg p-4">
              <div className="flex items-center justify-between">
                <div className="flex items-center">
                  <div className="text-red-800">
                    <p className="font-medium">Failed to load rules</p>
                    <p className="text-sm text-red-600 mt-1">{error}</p>
                  </div>
                </div>
                <button
                  onClick={() => {
                    clearError();
                    fetchRules();
                  }}
                  className="ml-4 px-4 py-2 bg-red-600 text-white text-sm rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors"
                >
                  Retry
                </button>
              </div>
            </div>
          </div>
        )}

        {/* Rules Table */}
        <div className="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
          <div className="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <div className="flex items-center">
              <Settings className="h-5 w-5 text-gray-500 mr-2" />
              <h2 className="text-lg font-semibold text-gray-900">
                Existing Rules ({rules.length})
              </h2>
            </div>
          </div>

          {rules.length === 0 ? (
            <div className="text-center py-16">
              <FileText className="mx-auto h-12 w-12 text-gray-400 mb-4" />
              <p className="text-gray-500 text-lg">No rules found</p>
              <p className="text-gray-400 text-sm">
                Use the Demo Data Seeder to populate sample rules
              </p>
            </div>
          ) : (
            <div className="overflow-x-auto">
              <table className="min-w-full divide-y divide-gray-200">
                <thead className="bg-gray-50">
                  <tr>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Rule Details
                    </th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Status
                    </th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Priority
                    </th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Valid Period
                    </th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Conditions
                    </th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Actions
                    </th>
                  </tr>
                </thead>
                <tbody className="bg-white divide-y divide-gray-200">
                  {rules.map((rule) => (
                    <tr
                      key={rule.id}
                      className="hover:bg-gray-50 transition-colors"
                    >
                      <td className="px-6 py-4 whitespace-nowrap">
                        <div>
                          <div className="text-sm font-medium text-gray-900">
                            {rule.name}
                          </div>
                          <div className="text-sm text-gray-500">
                            {rule.description}
                          </div>
                        </div>
                      </td>
                      <td className="px-6 py-4 whitespace-nowrap">
                        <div className="flex items-center">
                          <span
                            className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${
                              rule.is_active
                                ? "bg-green-100 text-green-800"
                                : "bg-red-100 text-red-800"
                            }`}
                          >
                            {rule.is_active ? (
                              <CheckCircle className="h-3 w-3 mr-1" />
                            ) : (
                              <XCircle className="h-3 w-3 mr-1" />
                            )}
                            {rule.is_active ? "Active" : "Inactive"}
                          </span>
                          {rule.stackable && (
                            <span className="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                              <Layers className="h-3 w-3 mr-1" />
                              Stackable
                            </span>
                          )}
                        </div>
                      </td>
                      <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <div className="flex items-center">
                          <Hash className="h-4 w-4 text-gray-400 mr-1" />
                          {rule.salience}
                        </div>
                      </td>
                      <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <div className="flex items-center">
                          <Calendar className="h-4 w-4 text-gray-400 mr-1" />
                          <div>
                            <div>From: {formatDate(rule.valid_from)}</div>
                            <div>Until: {formatDate(rule.valid_until)}</div>
                          </div>
                        </div>
                      </td>
                      <td className="px-6 py-4">
                        <div className="max-w-xs">
                          {formatConditions(rule.conditions)}
                        </div>
                      </td>
                      <td className="px-6 py-4">
                        <div className="max-w-xs">
                          {formatActions(rule.actions)}
                        </div>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          )}
        </div>
      </div>
    </div>
  );
};

export default RulesManagementPage;
