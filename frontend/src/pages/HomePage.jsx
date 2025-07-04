import React from "react";
import { Link } from "react-router-dom";
import {
  ShoppingCart,
  Settings,
  ArrowRight,
  RefreshCw,
  Layers,
  Target,
  Zap,
  Wrench,
  BarChart,
} from "lucide-react";

const HomePage = () => {
  return (
    <div className="min-h-screen bg-gray-50 py-12">
      <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div className="mb-12">
          <h1 className="text-4xl font-bold text-gray-900 mb-4">
            Rule-Based Promotion Engine
          </h1>
          <p className="text-xl text-gray-600 mb-8">
            A powerful promotion engine demonstrating dynamic discount
            calculations
          </p>
        </div>

        <div className="grid md:grid-cols-2 gap-8 mb-12">
          <Link
            to="/checkout"
            className="group bg-white rounded-xl shadow-lg p-8 hover:shadow-xl transition-all duration-300 border border-gray-100 hover:border-blue-200 block"
          >
            <div className="flex items-center justify-center w-16 h-16 bg-blue-100 rounded-xl mb-6 group-hover:bg-blue-200 transition-colors">
              <ShoppingCart className="h-8 w-8 text-blue-600" />
            </div>
            <h2 className="text-2xl font-semibold text-gray-900 mb-3">
              Checkout
            </h2>
            <p className="text-gray-600 mb-4">
              Experience real-time discount calculations as you select products
              and customers. See how promotion rules are applied dynamically to
              your cart.
            </p>
            <div className="flex items-center text-blue-600 font-medium group-hover:text-blue-700">
              Start Shopping
              <ArrowRight className="h-4 w-4 ml-2 group-hover:translate-x-1 transition-transform" />
            </div>
          </Link>

          <Link
            to="/rules"
            className="group bg-white rounded-xl shadow-lg p-8 hover:shadow-xl transition-all duration-300 border border-gray-100 hover:border-blue-200 block"
          >
            <div className="flex items-center justify-center w-16 h-16 bg-green-100 rounded-xl mb-6 group-hover:bg-green-200 transition-colors">
              <Settings className="h-8 w-8 text-green-600" />
            </div>
            <h2 className="text-2xl font-semibold text-gray-900 mb-3">
              Rules Management
            </h2>
            <p className="text-gray-600 mb-4">
              View existing promotion rules and create new ones. Manage the
              conditions and actions that drive your promotional campaigns.
            </p>
            <div className="flex items-center text-green-600 font-medium group-hover:text-green-700">
              Manage Rules
              <ArrowRight className="h-4 w-4 ml-2 group-hover:translate-x-1 transition-transform" />
            </div>
          </Link>
        </div>

        <div className="bg-white rounded-xl shadow-lg p-8 border border-gray-100">
          <h2 className="text-2xl font-semibold text-gray-900 mb-6">
            System Features
          </h2>
          <div className="grid md:grid-cols-3 gap-8 text-left">
            <div className="flex flex-col items-center text-center">
              <div className="flex items-center justify-center w-12 h-12 bg-blue-100 rounded-lg mb-4">
                <RefreshCw className="h-6 w-6 text-blue-600" />
              </div>
              <h3 className="font-semibold text-gray-900 mb-2">
                Real-time Evaluation
              </h3>
              <p className="text-sm text-gray-600">
                Instant discount calculations as you modify your cart
              </p>
            </div>
            <div className="flex flex-col items-center text-center">
              <div className="flex items-center justify-center w-12 h-12 bg-green-100 rounded-lg mb-4">
                <Layers className="h-6 w-6 text-green-600" />
              </div>
              <h3 className="font-semibold text-gray-900 mb-2">
                Rule Stacking
              </h3>
              <p className="text-sm text-gray-600">
                Multiple promotions can be applied to a single order
              </p>
            </div>
            <div className="flex flex-col items-center text-center">
              <div className="flex items-center justify-center w-12 h-12 bg-purple-100 rounded-lg mb-4">
                <Target className="h-6 w-6 text-purple-600" />
              </div>
              <h3 className="font-semibold text-gray-900 mb-2">
                Smart Targeting
              </h3>
              <p className="text-sm text-gray-600">
                Customer-specific and product-specific promotion rules
              </p>
            </div>
            <div className="flex flex-col items-center text-center">
              <div className="flex items-center justify-center w-12 h-12 bg-yellow-100 rounded-lg mb-4">
                <Zap className="h-6 w-6 text-yellow-600" />
              </div>
              <h3 className="font-semibold text-gray-900 mb-2">
                High Performance
              </h3>
              <p className="text-sm text-gray-600">
                Node.js rule engine for fast evaluation
              </p>
            </div>
            <div className="flex flex-col items-center text-center">
              <div className="flex items-center justify-center w-12 h-12 bg-red-100 rounded-lg mb-4">
                <Wrench className="h-6 w-6 text-red-600" />
              </div>
              <h3 className="font-semibold text-gray-900 mb-2">
                Flexible Rules
              </h3>
              <p className="text-sm text-gray-600">
                JSON-based rule configuration system
              </p>
            </div>
            <div className="flex flex-col items-center text-center">
              <div className="flex items-center justify-center w-12 h-12 bg-indigo-100 rounded-lg mb-4">
                <BarChart className="h-6 w-6 text-indigo-600" />
              </div>
              <h3 className="font-semibold text-gray-900 mb-2">
                Clear Breakdown
              </h3>
              <p className="text-sm text-gray-600">
                Detailed view of applied discounts and savings
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default HomePage;
