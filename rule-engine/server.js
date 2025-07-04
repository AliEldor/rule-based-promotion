const express = require("express");
const { Engine } = require("json-rules-engine");
const cors = require("cors");

const app = express();
const PORT = process.env.PORT || 3000;

// middleware
app.use(express.json());
app.use(cors());

// health check endpoint
app.get("/health", (req, res) => {
  res.json({
    status: "healthy",
    timestamp: new Date().toISOString(),
    service: "rule-engine",
  });
});

app.post("/evaluate", async (req, res) => {
  try {
    const { rule, facts } = req.body;

    if (!rule || !facts) {
      return res.status(400).json({
        error: "Missing rule or facts in request body",
      });
    }

    // create engine instance
    const engine = new Engine();

    engine.addRule(rule);

    const results = await engine.run(facts);

    // process rslts and calc discount
    let matched = false;
    let discount = 0;
    let metadata = {};

    if (results.events && results.events.length > 0) {
      matched = true;
      const event = results.events[0];

      // Calc discnt based on actions
      discount = calculateDiscount(event.params.actions, facts);

      metadata = {
        ruleId: event.params.ruleId,
        ruleName: event.params.ruleName,
        lineTotal: event.params.lineTotal,
      };
    }

    res.json({
      matched,
      discount: Math.round(discount * 100) / 100,
      metadata,
    });
  } catch (error) {
    console.error("Rule evaluation error:", error);
    res.status(500).json({
      error: "Rule evaluation failed",
      details: error.message,
    });
  }
});

function calculateDiscount(actions, facts) {
  let totalDiscount = 0;
  const lineTotal = facts.line.total;
  const unitPrice = facts.line.unitPrice;
  const quantity = facts.line.quantity;

  for (const action of actions) {
    switch (action.type) {
      case "percent":
        totalDiscount += lineTotal * (action.value / 100);
        break;

      case "fixed":
        totalDiscount += action.value;
        break;

      case "free_units":
        totalDiscount += unitPrice * action.value;
        break;

      case "tiered_percent":
        const tier = findApplicableTier(quantity, action.tiers || []);
        if (tier) {
          totalDiscount += lineTotal * (tier.discount_percent / 100);
        }
        break;

      default:
        console.warn(`Unknown action type: ${action.type}`);
    }
  }

  return totalDiscount;
}

function findApplicableTier(quantity, tiers) {
  for (const tier of tiers) {
    if (
      quantity >= tier.min_quantity &&
      (tier.max_quantity === null || quantity <= tier.max_quantity)
    ) {
      return tier;
    }
  }
  return null;
}

// err handling middleware
app.use((error, req, res, next) => {
  console.error("Unhandled error:", error);
  res.status(500).json({
    error: "Internal server error",
    details: error.message,
  });
});

app.use("*", (req, res) => {
  res.status(404).json({
    error: "Endpoint not found",
    path: req.originalUrl,
  });
});

app.listen(PORT, () => {
  console.log("server is running on port", PORT);
});

module.exports = app;
