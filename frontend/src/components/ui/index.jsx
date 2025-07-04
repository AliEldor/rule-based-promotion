import React from "react";
import { Loader2, AlertCircle, CheckCircle } from "lucide-react";

export const LoadingSpinner = ({ size = "md", text = "Loading..." }) => {
  const sizeClasses = {
    sm: "w-4 h-4",
    md: "w-6 h-6",
    lg: "w-8 h-8",
  };

  return (
    <div className="flex items-center justify-center py-4">
      <Loader2 className={`animate-spin text-blue-600 ${sizeClasses[size]}`} />
      {text && <span className="ml-3 text-gray-600 text-sm">{text}</span>}
    </div>
  );
};

export const ErrorMessage = ({ message, onRetry }) => {
  return (
    <div className="bg-red-50 border border-red-200 rounded-lg p-4">
      <div className="flex">
        <div className="flex-shrink-0">
          <AlertCircle className="h-5 w-5 text-red-400" />
        </div>
        <div className="ml-3">
          <p className="text-sm text-red-800">{message}</p>
          {onRetry && (
            <button
              onClick={onRetry}
              className="mt-2 text-sm text-red-600 underline hover:text-red-800 focus:outline-none transition-colors"
            >
              Try again
            </button>
          )}
        </div>
      </div>
    </div>
  );
};

export const SuccessMessage = ({ message }) => {
  return (
    <div className="bg-green-50 border border-green-200 rounded-lg p-4">
      <div className="flex">
        <div className="flex-shrink-0">
          <CheckCircle className="h-5 w-5 text-green-400" />
        </div>
        <div className="ml-3">
          <p className="text-sm text-green-800">{message}</p>
        </div>
      </div>
    </div>
  );
};
